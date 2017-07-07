<?php
namespace Tests;

use Cache\Adapter\PHPArray\ArrayCachePool;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Praline\ContainerIds;
use Praline\Session\SessionDataInterface;
use Praline\Session\SessionManager;
use Psr\Log\NullLogger;
use Tests\Common\UserInfo;

class SessionSessionManagerTest extends TestCase
{
    public function testSessionManager()
    {
        $container = new Container();
        $container[ContainerIds::LOGGER] = new NullLogger();

        $pool = new ArrayCachePool();
        $sessionManager = new SessionManager($pool, $container);

        $user1 = new UserInfo(7, 'Alice');
        $session1 = $sessionManager->newSession($user1);

        $token1 = $session1->getNextAccessToken();

        $found1 = $sessionManager->findSession($token1);

        /** @var UserInfo $cached1 */
        $cached1 = $found1->getData();

        $this->assertUserInfo($user1, $cached1);
    }

    private function assertUserInfo(UserInfo $user, UserInfo $cached)
    {
        $this->assertEquals($user->id, $cached->id);
        $this->assertEquals($user->name, $cached->name);
        $this->assertEquals($user->getUniqueKey(), $cached->getUniqueKey());
    }
}
