<?php
namespace Praline\DateTime\Clocks;

use Praline\DateTime\ClockInterface;
use Praline\DateTime\Duration;

// 將一個時鐘的時間加上偏差值，可用於伺服環境的測試
class OffsetClock implements ClockInterface
{
    /** @var  ClockInterface */
    private $referenceClock;

    /** @var  \DateInterval */
    private $offset;

    public function __construct(ClockInterface $referenceClock, $offset)
    {
        $this->referenceClock = $referenceClock;

        if (is_string($offset)) {
            $this->offset = new \DateInterval($offset);
        } else if ($offset instanceof \DateInterval) {
            $this->offset = $offset;
        } else {
            throw new \TypeError('Not a string or DateInterval');
        }
    }

    public static function byWeeks(ClockInterface $referenceClock, int $weeks): OffsetClock
    {
        return new OffsetClock($referenceClock, Duration::weeks($weeks));
    }

    public static function byDays(ClockInterface $referenceClock, int $days): OffsetClock
    {
        return new OffsetClock($referenceClock, Duration::days($days));
    }

    public function now(): \DateTime
    {
        return $this->referenceClock->now()->add($this->offset);
    }
}
