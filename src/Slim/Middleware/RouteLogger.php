<?php
namespace Praline\Slim\Middleware;

use Praline\Slim\Middleware;
use Slim\Http\{Request, Response, Uri};

// Slim Framework Middleware - Route 用 Logger
// 1. API 呼叫與回應的歷程記錄
// 2. 攔截異常，輸出錯誤訊息
// 通常會是最外層的 middleware
// 選項：
// - ignoreMethods：這些 methods 跳過不記錄，常用於排除 CORS 的 OPTIONS 要求。
//                  注意 method 名稱必須是全大寫
class RouteLogger extends Middleware
{
    /** @var array */
    private $ignoreMethods = [];

    public function __construct($container, $options = [])
    {
        parent::__construct($container);

        if (array_key_exists('ignoreMethods', $options)) {
            $this->ignoreMethods = $options['ignoreMethods'];
        }
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        /** @var Uri $uri */
        $uri = $request->getUri();

        $pathAndQuery = empty($uri->getQuery())
                      ? $uri->getPath()
                      : $uri->getPath() . '?' . $uri->getQuery();

        $method = $request->getMethod();
        $ignored = in_array($method, $this->ignoreMethods);

        if (!$ignored) {
            $this->logger->info($method . ' ' . $pathAndQuery);
        }

        $response = $this->processRequestAndCatchAll($request, $response, $next);

        if (!$ignored) {
            $this->logger->info($response->getStatusCode());
        }

        return $response;
    }
}
