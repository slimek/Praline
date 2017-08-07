<?php
namespace Tests;

use Cache\Adapter\PHPArray\ArrayCachePool;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Praline\ContainerIds;
use Praline\Session\SessionManager;
use Praline\Slim\Middleware\SessionAuthorizer;
use Psr\Log\NullLogger;
use Slim\Http\
{
    Environment, Headers, Request, RequestBody, Response, UploadedFile, Uri
};
use Tests\Common\UserInfo;

class SlimMiddlewareSessionAuthorizerTest extends TestCase
{
    public function testSessionAuthorizer()
    {
        $container = new Container();
        $container[ContainerIds::LOGGER] = new NullLogger();

        $pool = new ArrayCachePool();
        $sessionManager = new SessionManager($pool, $container);
        $container[ContainerIds::SESSION_MANAGER] = $sessionManager;

        $middleware = new SessionAuthorizer($container);

        // 登入, 取得第 1 個 access token

        $user = new UserInfo(2, 'Reimu');
        $session = $sessionManager->newSession($user);
        $token1 = $session->getNextAccessToken();

        // 第一次呼叫 API，使用登入取得的 access token

        $request1 = static::makeRequest();
        $request1 = $request1->withHeader('Authorization', 'Bearer ' . $token1);

        $responseRaw1 = new Response();

        $response1 = $middleware($request1, $responseRaw1, function (Request $req, $res) {
            $user = $req->getAttribute(UserInfo::ATTRIBUTE_NAME);
            $this->assertEquals('Reimu', $user->name);
            return $res;
        });

        $this->assertEquals(200, $response1->getStatusCode());
        $this->assertTrue($response1->hasHeader(SessionManager::NEXT_ACCESS_TOKEN_HEADER_NAME));

        $token2 = $response1->getHeaderLine(SessionManager::NEXT_ACCESS_TOKEN_HEADER_NAME);

        // 第二次呼叫 API，使用第一次呼叫取得的 access token

        $request2 = static::makeRequest();
        $request2 = $request2->withHeader('Authorization', 'Bearer ' . $token2);

        $responseRaw2 = new Response();

        $response2 = $middleware($request2, $responseRaw2, function (Request $req, $res) {
            $user = $req->getAttribute(UserInfo::ATTRIBUTE_NAME);
            $this->assertEquals('Reimu', $user->name);
            return $res;
        });

        $this->assertEquals(200, $response2->getStatusCode());
        $this->assertTrue($response2->hasHeader(SessionManager::NEXT_ACCESS_TOKEN_HEADER_NAME));
    }

    private static function makeRequest(): Request
    {
        $env = Environment::mock();
        $uri = Uri::createFromString('https://example.com/foo/bar?abc=123');
        $headers = Headers::createFromEnvironment($env);
        $cookies = [];
        $serverParams = $env->all();
        $body = new RequestBody();
        $uploadedFiles = UploadedFile::createFromEnvironment($env);

        return new Request('GET', $uri, $headers, $cookies, $serverParams, $body, $uploadedFiles);
    }
}