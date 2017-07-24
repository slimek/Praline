<?php
namespace Praline\DateTime;

use Praline\DateTime\Clocks\SystemClock;

// 提供程式內可簡單呼叫的預設時鐘
// 命名參考自 GitHub 上的 Brick\DateTime
class Clock
{
    /** @var ClockInterface */
    private static $currentClock;

    /** @var ClockInterface[] */
    private static $clockStack = [];

    public static function now(): \DateTime
    {
        if (is_null(static::$currentClock)) {
            static::$currentClock = new SystemClock();
        }

        return static::$currentClock->now();
    }

    // 將 now() 轉換為 ISO 8601 字串的便利函式
    // 實際上是 \DateTime::ATOM 格式，會帶有時區部分，不帶毫秒
    public static function isoNow(): string
    {
        return static::now()->format(\DateTime::ATOM);
    }

    // 將 now() 轉換為資料庫（主要指 MySQL）可以接受的時間日期格式，也就是不帶時區部分，毫秒部份也略去
    public static function dbNow(): string
    {
        return static::now()->format('Y-m-d H:i:s');
    }

    // 取代目前的時鐘
    public static function setCurrent(ClockInterface $newClock)
    {
        static::$currentClock = $newClock;
    }

    // 推入新時鐘以取代目前使用的時鐘
    // - 單元測試的時候會比較方便
    public static function pushClock(ClockInterface $newClock)
    {
        array_push(static::$clockStack, static::$currentClock);
        static::$currentClock = $newClock;
    }

    // 將先前因為 pushClock() 而存放在內的時鐘還原
    public static function popClock()
    {
        if (empty(static::$clockStack)) {
            throw new \Exception('No clock in the stack');
        }

        static::$currentClock = array_pop(static::$clockStack);
    }
}
