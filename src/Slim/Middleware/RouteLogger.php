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

// Slim Framework Middleware - Route 用 Logger
// 1. API 呼叫與回應的歷程記錄
// 2. 攔截異常，輸出錯誤訊息
// 通常會是最外層的 middleware
class RouteLogger
{
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

        } catch (\Throwable $t) {

            $this->logger->error($t->getMessage() . PHP_EOL . $t->getTraceAsString());

            $response = $response->withStatus(500)
                                 ->write($t->getMessage());
        }

        $this->logger->info($response->getStatusCode());

        return $response;
    }
}
