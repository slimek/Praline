<?php
namespace Praline\Slim;

use Praline\ContainerIds;
use Praline\Error\{BadRequest, UserError};
use Psr\Log\LoggerInterface;
use Slim\Http\{Request, Response};

// 用於 Slim Framework 之中，各 Middleware 的基底類別
class Middleware
{
    use ResponseHelperTrait;

    /** @var  LoggerInterface */
    protected $logger;

    public function __construct($container)
    {
        $this->logger = $container[ContainerIds::LOGGER];
    }

    // 產生一個代表錯誤狀況的資料，可以直接用於產生 response
    protected function makeErrorResult(int $errorCode, string $message)
    {
        return [
            'error' => [
                'code' => $errorCode,
                'message' => $message,
            ],
        ];
    }

    // 從 Throwable 異常物件產生出一個代表錯誤狀況的資料，可以直接用於產生 response
    // 通常我們會直接使用 Throwable 的 code
    // 不過也可以用 replaceErrorCode 將錯誤代碼替換掉
    protected function makeResultFromThrowable(\Throwable $t, $replaceErrorCode = null): array
    {
        return [
            'error' => [
                'code' => $replaceErrorCode ?? $t->getCode(),
                'message' => $t->getMessage(),
            ],
        ];
    }

    // 需要攔截 Praline 及 PHP 系統異常的 middleware，都可以使用這個函式
    protected function processRequestAndCatchAll(Request $request, Response $response, callable $next)
    {
        try
        {
            $response = $next($request, $response);

        } catch (UserError $ue) {

            // 使用者造成的錯誤，log 等級不需要到警告
            $this->logger->debug(
                $ue->getMessage() . "({$ue->getCode()})"
            );

            $response = $this->withJson($response, $this->makeResultFromThrowable($ue), $ue->getHttpStatusCode());

        } catch (BadRequest $br) {

            // Client 側程式錯誤，應當輸出 log 以協助 client 偵錯。

            $this->logger->warning(
                $br->getMessage() . PHP_EOL . $br->getTraceAsString()
            );

            $response = $this->withJson($response, $this->makeResultFromThrowable($br), 400);

        } catch (\Throwable $t) {

            // Server 側程式錯誤或運行期錯誤

            $this->logger->error($t->getMessage() . PHP_EOL . $t->getTraceAsString());

            $response = $this->withJson($response, $this->makeResultFromThrowable($t), 500);
        }

        return $response;
    }
}
