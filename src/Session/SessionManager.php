<?php
namespace Praline\Session;

use Praline\ContainerIds;
use Praline\Utils\Cache;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

// 集中管理 Sessions 物件
// 通常會在 dependency injection 階段放進 container 裡面，以便給 SessionAuthorizer 使用
// 放進 container 裡面時，請使用 ContainerIds::SESSION_MANAGER
class SessionManager
{
    // 產生回應時，要將 next access token 放進 response header 裡面
    // - 為何這常數不放在 Session？因為在將 header 放進 response 的這個動作所在的 PHP 檔案裡，
    //   你通常會 use SessionManager 卻不需要 use Session ～ :p
    public const NEXT_ACCESS_TOKEN_HEADER_NAME = 'X-Next-Access-Token';

    /** @var Cache */
    private $cache;

    /** @var  LoggerInterface */
    private $logger;

    public function __construct(CacheItemPoolInterface $cachePool, $container)
    {
        $this->cache = new Cache($cachePool);

        $this->logger = $container[ContainerIds::LOGGER];
    }

    public function newSession(SessionDataInterface $sessionData): Session
    {
        $session = new Session($sessionData);
        $sessionId = $session->getId();

        // 為了避免和 sessionId（hex 字串）重覆，額外加上一個前綴
        $uniqueKey = 'key_' . $session->getData()->getUniqueKey();

        // 找出該使用者的舊 session，將之刪除
        $oldSessionId = $this->cache->load($uniqueKey);
        if (!is_null($oldSessionId)) {
            $this->cache->delete($oldSessionId);
        }

        while ($this->cache->save($sessionId, $session) === false) {
            // Session ID 重覆了，重新產生一個新的 Session 物件
            $session = new Session($sessionData);
            $sessionId = $session->getId();
        }

        // 將新的 sessionId 存起來
        $this->cache->save($uniqueKey, $sessionId);

        return $session;
    }

    // 使用 base64 編碼的 access token 來尋找相應的 session，
    // 只要 access token 是 next 或 cached 其中之一，就算通過
    // 接下來要怎麼處理交給呼叫端決定
    public function findSession(string $accessToken): ?Session
    {
        $accessTokenInfo = AccessTokenInfo::fromBase64($accessToken);
        if (is_null($accessTokenInfo)) {
            $this->logger->debug('Invalid access token');
            return null;
        }

        $sessionId = $accessTokenInfo->getSessionId();

        $session = $this->cache->load($sessionId);
        if (is_null($session)) {
            $this->logger->debug('Session not found');
            return null;
        }

        if (!$session->matchAccessToken($accessToken)) {
            $this->logger->debug('Access token does not match with session');
            return null;
        }

        return $session;
    }

    public function updateSession(Session $session)
    {
        $this->cache->save($session->getId(), $session);
    }
}
