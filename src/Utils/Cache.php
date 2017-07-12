<?php
namespace Praline\Utils;

use Psr\Cache\CacheItemPoolInterface;

// 將 PSR-6 的 cache 介面包裝成比較容易理解的 key-value 形式
// 整個 Cache 中個別元素的存活時間是固定的，由建構子的 duration 參數決定，省略的話預設 1 小時
class Cache
{
    /** @var  CacheItemPoolInterface */
    private $pool;

    /** @var  \DateInterval - 快取的存活時間 */
    private $duration;

    // 如果沒給 duration 參數，預設時間為 1 小時
    public function __construct(CacheItemPoolInterface $pool, \DateInterval $duration = null)
    {
        $this->pool = $pool;

        if (is_null($duration)) {
            $this->duration = new \DateInterval('PT1H');
        } else {
            $this->duration = $duration;
        }
    }

    // 讀取資料，若沒找到的話傳回 null
    public function load(string $key)
    {
        return $this->pool->getItem($key)->get();
    }

    // 寫入資料
    // - 可以用在新增資料、也可以用在更新已有的資料，到期時間會重新計算
    public function save(string $key, $value)
    {
        $item = $this->pool->getItem($key);
        $item->set($value);
        $item->expiresAt($this->generateExpireTime());
        $saved = $this->pool->save($item);
        if ($saved === false) {
            throw new \Exception("Save cache item failed, key: '$key'");
        }
    }

    // 互斥寫入資料
    // - 如果資料已經存在，就放棄覆寫資料並傳回 false
    //   主要用於 key 是隨機產生的時候，避免使用到重覆的 key
    public function exclusiveSave(string $key, $value): bool
    {
        $item = $this->pool->getItem($key);
        if ($item->isHit()) {
            return false;
        }

        $item->set($value);
        $item->expiresAt($this->generateExpireTime());
        $saved = $this->pool->save($item);
        if ($saved === false) {
            throw new \Exception("Save cache failed, key: '$key'");
        }

        return true;
    }

    // 刪除指定資料，如果成功的話傳回 true，該資料不存在傳回 false
    public function delete(string $key): bool
    {
        $item = $this->pool->getItem($key);
        if ($item->isHit()) {
            $deleted = $this->pool->deleteItem($key);
            if ($deleted === false) {
                throw new \Exception("Delete cache failed, key: '$key'");
            }
            return true;
        } else {
            return false;
        }
    }

    public function getDuration(): \DateInterval
    {
        return $this->duration;
    }

    // 產生從現在算起的到期時間
    private function generateExpireTime(): \DateTime
    {
        $now = new \DateTime();
        return $now->add($this->duration);
    }
}
