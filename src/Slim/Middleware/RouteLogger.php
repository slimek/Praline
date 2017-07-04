<?php
namespace Praline\Slim\Middleware;

// Composer
use Psr\Log\LoggerInterface;
use Slim\Http\
{
    Request, Response, Uri
};

// Praline
use Praline\ContainerIds;
use Praline\Error\{BadRequest, UserError};

// Slim Framework Middleware - Route 用 Logger
// 1. API 呼叫與回應的歷程記錄
// 2. 攔截異常，輸出錯誤訊息
// 通常會是最外層的 middleware
class RouteLogger
{
    use \Praline\Slim\ResponseHelperTrait;

    /** @var  LoggerInterface */
    private $logger;

    public function __construct($container)
    {
        $this->logger = $container[ContainerIds::LOGGER];
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        /** @var Uri $uri */
        $uri = $request->getUri();

        $pathAndQuery = empty($uri->getQuery())
                      ? $uri->getPath()
                      : $uri->getPath() . '?' . $uri->getQuery();

        $this->logger->info($pathAndQuery);

        try
        {
            $response = $next($request, $response);

        } catch (UserError $ue) {

            // 使用者造成的錯誤，log 等級不需要到警告
            $this->logger->debug(
                $ue->getMessage() . "({$ue->getCode()})"
            );

            $response = $this->withJson($response, $this->makeResult($ue), $ue->getHttpStatusCode());

        } catch (BadRequest $br) {

            // Client 側程式錯誤，應當輸出 log 以協助 client 偵錯。

            $this->logger->warning(
                $br->getMessage() . PHP_EOL . $br->getTraceAsString()
            );

            $response = $this->withJson($response, $this->makeResult($br), 400);

        } catch (\Throwable $t) {

            // Server 側程式錯誤或運行期錯誤

            $this->logger->error($t->getMessage() . PHP_EOL . $t->getTraceAsString());

            $response = $this->withJson($response, $this->makeResult($t), 500);
        }

        $this->logger->info($response->getStatusCode());

        return $response;
    }

    // code 可省略：通常我們會直接使用 Throwable 的 code
    // 不過衍生類別或許會想針對特定的錯誤另定不同的 code
    protected function makeResult(\Throwable $t, $code = null)
    {
        return [
          'error' => [
              'code' => $code ?? $t->getCode(),
              'message' => $t->getMessage(),
          ],
        ];
    }
}
