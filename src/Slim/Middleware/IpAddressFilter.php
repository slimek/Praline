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

    public function __construct($container, array $allowedIpAddresses)
    {
        parent::__construct($container);

        $this->allowedIpAddresses = $allowedIpAddresses;

        $this->ipAddressMiddleware = new IpAddress();
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
