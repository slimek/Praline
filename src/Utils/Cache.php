<?php
namespace Praline\Utils;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

// 將 PSR-6 的 cache 介面包裝成比較容易理解的 key-value 形式
class Cache
{
    /** @var  CacheItemPoolInterface */
    private $pool;

    /** @var  string - 符合 DateTime::modify() 參數格式的時間長度，代表預設快取時間 */
    private $expiration;

    // expiration 必須是符合 DateTime::modify() 參數格式的時間長度，例如 +10 minute
    public function __construct(CacheItemPoolInterface $pool, string $expiration = null)
    {
        $this->pool = $pool;

        if (is_null($expiration)) {
            $this->expiration = '+1 hour';
        }
    }

    // 讀取資料，若沒找到的話傳回 null
    public function load(string $key)
    {
        return $this->pool->getItem($key)->get();
    }

    // 寫入資料
    // - 可以用在新增資料、也可以用在更新已有的資料，到期時間會重新計算
    //   沒給 expireTime 的話會自行產生
    public function save(string $key, $value, \DateTime $expireTime = null)
    {
        if (is_null($expireTime)) {
            $expireTime = $this->generateExpireTime();
        }

        $item = $this->pool->getItem($key);
        $item->set($value);
        $item->expiresAt($expireTime);
        $saved = $this->pool->save($item);
        if ($saved === false) {
            throw new \Exception("Save cache item failed, key: '$key'");
        }
    }

    // 互斥寫入資料
    // - 如果資料已經存在，就放棄覆寫資料並傳回 false
    //   主要用於 key 是隨機產生的時候，避免使用到重覆的 key
    public function exclusiveSave(string $key, $value, \DateTime $expireTime = null): bool
    {
        if (is_null($expireTime)) {
            $expireTime = $this->generateExpireTime();
        }

        $item = $this->pool->getItem($key);
        if ($item->isHit()) {
            return false;
        }

        $item->set($value);
        $item->expiresAt($expireTime);
        $saved = $this->pool->save($item);
        if ($saved === false) {
            throw new \Exception("Save cache failed, key: '$key'");
        }

        return true;
    }

    // 產生從現在算起的到期時間
    private function generateExpireTime(): \DateTime
    {
        $now = new \DateTime();
        return $now->modify($this->expiration);
    }
}
