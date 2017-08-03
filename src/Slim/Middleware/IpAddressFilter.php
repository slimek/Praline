<?php
namespace Praline\Slim\Middleware;

// Composer
use RKA\Middleware\IpAddress;
use Slim\Http\{Request, Response};

// Praline
use Praline\Slim\Middleware;

// 僅容許來源 IP address 在白名單中的要求通過
// - 基於 akrabat/rka-ip-address-middleware
class IpAddressFilter extends Middleware
{
    /** @var  array */
    private $allowedIpAddresses;

    /** @var  IpAddress */
    private $ipAddressMiddleware;

    // 選項：
    // - checkProxyHeaders：允許從 x-forwarded-for 標頭中取得轉發的來源位址。注意：此 IP 可以偽造，不可用於有安全顧慮的場合！
    //   trustedProxies：允許使用轉發位址的 proxies 位址。如果沒指定的話，就是接受任意 proxies。
    public function __construct($container, array $allowedIpAddresses, array $options = [])
    {
        parent::__construct($container);

        $this->allowedIpAddresses = $allowedIpAddresses;

        $checkProxyHeaders = $options['checkProxyHeaders'] ?? false;
        $trustedProxies = $options['trustedProxies'] ?? [];

        $this->ipAddressMiddleware = new IpAddress($checkProxyHeaders, $trustedProxies);
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        return ($this->ipAddressMiddleware)(
            $request,
            $response,
            function (Request $request, Response $response) use ($next) {

            $ipAddress = $request->getAttribute('ip_address');

            $found = array_search($ipAddress, $this->allowedIpAddresses);
            if ($found === false) {
                return $response->withStatus(403);
            }

            return $next($request, $response);
        });
    }
}
