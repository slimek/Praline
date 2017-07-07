<?php
namespace Tests;

use Cache\Adapter\PHPArray\ArrayCachePool;
use PHPUnit\Framework\TestCase;
use Praline\Utils\Cache;
use Tests\Common\BookInfo;
use Tests\Common\UserInfo;

class UtilsCacheTest extends TestCase
{
    public function testCache()
    {
        $pool = new ArrayCachePool();
        $cache = new Cache($pool);

        // 加入資料
        $user1 = new UserInfo(7, 'Alice');
        $cache->save('user', $user1);

        /** @var UserInfo $cached1 */
        $cachedU1 = $cache->load('user');
        $this->assertEquals(7, $cachedU1->id);
        $this->assertEquals('Alice', $cachedU1->name);

        // 可以用來呼叫有 type hint 的函式嗎？
        // - 目前看來，Cache 介面在序列化的時候會保存類別資訊的樣子
        $this->assertUserInfo($user1, $cachedU1);

        /** @var UserInfo $notExist */
        $notExist = $cache->load('none');
        $this->assertNull($notExist);

        // 放個不同類別的東西進去
        $book1 = new BookInfo('Momo', 'Michael Ende');
        $cache->save('book', $book1);

        $cachedB1 = $cache->load('book');
        $this->assertEquals('Momo', $cachedB1->getTitle());
        $this->assertEquals('Michael Ende', $cachedB1->getAuthor());

        // 將快取的東西替換掉
        $user2 = new UserInfo(9, 'Yukari');
        $cache->save('user', $user2);

        $cachedU2 = $cache->load('user');
        $this->assertUserInfo($user2, $cachedU2);

        // 互斥寫入，不能寫已經存在的物件進去
        $user3 = new UserInfo(2, 'Reimu');
        $saved = $cache->exclusiveSave('user', $user3);
        $this->assertFalse($saved);

        // 刪除物件
        $deleted = $cache->delete('user');
        $this->assertTrue($deleted);

        // 可以互斥寫入了
        $saved = $cache->exclusiveSave('user', $user3);
        $this->assertTrue($saved);

        // 刪除不存在的物件，傳回 false
        $deleted = $cache->delete('none');
        $this->assertFalse($deleted);
    }

    private function assertUserInfo(UserInfo $user, UserInfo $cached)
    {
        $this->assertEquals($user->id, $cached->id);
        $this->assertEquals($user->name, $cached->name);
    }
}
