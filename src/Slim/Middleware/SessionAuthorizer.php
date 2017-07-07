<?php
namespace Praline\Slim\Middleware;

// Composer
use Slim\Http\{Request, Response};

// Praline
use Praline\ContainerIds;
use Praline\Error\ErrorCode;
use Praline\Session\SessionDataInterface;
use Praline\Session\SessionManager;
use Praline\Slim\Middleware;
use Praline\Utils\Stringy as S;

// 提供身份認證、要求歷程記錄、異常攔截等多種功能的 Slim Framework middleware
//   1. 使用 Authorization header 攜帶的 access token 來認證身份
//   2. 將回應內容記錄在 Session 資料之中，以應付 API 重複呼叫的情況
//   3. 使用 Next-Access-Token header 將下一次的 access token 傳回
//   4. 攔截異常並轉換為錯誤訊息的回應
// 通常會加掛在僅次於 RouteLogger，從外數來第二層的位置
//
class SessionAuthorizer extends Middleware
{
    /** @var  SessionManager */
    private $sessionManager;

    public function __construct($container)
    {
        parent::__construct($container);

        $this->sessionManager = $container[ContainerIds::SESSION_MANAGER];
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        // Step 1：先進行身份認證
        // - 如果認證成功的話，在輸出的 request log 之中加入足以識別身份的資訊

        $headerValues = $request->getHeader('Authorization');

        if (empty($headerValues)) {
            // Client 沒送必要的 header 過來？

            $this->logger->debug('Header Authorization is missing');

            return $this->withJson(
                $response,
                $this->makeErrorResult(ErrorCode::MISSING_HEADER, "Header 'Authorization' is missing"),
                401
            )->withHeader('WWW-Authentication', 'Bearer');
        }

        $headerValue = S::create($headerValues[0])->collapseWhitespace();
        $type = strval($headerValue->beforeFirst(' '));
        if ($type !== 'Bearer') {
            // Authorization type 不正確

            $this->logger->debug("Authorization type is not Bearer: '$type'");

            return $this->withJson(
                $response,
                $this->makeErrorResult(ErrorCode::INVALID_PARAMETER, 'Authorization type must be Berear'),
                401
            )->withHeader('WWW-Authentication', 'Bearer');
        }

        $currAccessToken = strval($headerValue->afterFirst(' '));

        $session = $this->sessionManager->findSession($currAccessToken);
        if (is_null($session)) {
            // 身份認證失敗 - 可能是 access token 不正確、過期、或者 session 過期

            $this->logger->debug('Session not found');

            return $this->withJson(
                $response,
                $this->makeErrorResult(ErrorCode::SESSION_NOT_FOUND, 'Session not found'),
                403
            );
        }

        $sessionData = $session->getData();
        $uri = $request->getUri();
        $this->logger->info($uri->getPath() . ' ' . $sessionData->getUniqueKey());

        // Step 2：判斷是否要直接回傳上次的結果

        if ($session->getCachedAccessToken() === $currAccessToken) {

            $json = $session->getCachedResponseJson();

            if (is_null($json)) {
                // 相同的要求正在處理中？
                return $this->withJson(
                    $response,
                    $this->makeErrorResult(ErrorCode::REQUEST_CONFLICT, 'Request conflict'),
                    409
                );
            } else {

                $this->logger->debug('Response JSON (cached): ' . $json);

                return $response
                    ->write($json)
                    ->withHeader('Content-Type', 'application/json;charset=utf-8')
                    ->withHeader(SessionManager::NEXT_ACCESS_TOKEN_HEADER_NAME, $session->getNextAccessToken());
            }
        }

        // 既然確認是新的要求，就替換 access token
        $session->advanceAccessToken();
        $this->sessionManager->updateSession($session);

        // Step 3：處理要求，並將回應快取

        $request = $request->withAttribute(SessionDataInterface::ATTRIBUTE_NAME, $sessionData);

        $response = $this->processRequestAndCatchAll($request, $response, $next);

        $json = strval($response->getBody());

        $this->logger->debug('Response JSON: ' . $json);

        $nextAccessToken = $session->setResponseJson($json);
        $this->sessionManager->updateSession($session);

        return $response->withHeader(SessionManager::NEXT_ACCESS_TOKEN_HEADER_NAME, $nextAccessToken);
    }
}
