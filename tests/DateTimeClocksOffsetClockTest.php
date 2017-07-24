<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Praline\DateTime\Clock;
use Praline\DateTime\Clocks\FixedClock;
use Praline\DateTime\Clocks\OffsetClock;

class DateTimeClocksOffsetClockTest extends TestCase
{
    public function testOffsetClock()
    {
        $fc = new FixedClock('2017-04-05 16:30');

        $c1 = new OffsetClock($fc, new \DateInterval('P1D'));
        $this->assertEquals(new \DateTime('2017-04-06 16:30'), $c1->now());

        $c2 = new OffsetClock($fc, 'PT6H');
        $this->assertEquals(new \DateTime('2017-04-05 22:30'), $c2->now());

        // 配置給全域 Clock

        Clock::setCurrent($c1);
        $this->assertEquals(new \DateTime('2017-04-06 16:30'), $c1->now());
        $this->assertEquals('2017-04-06 16:30:00', Clock::dbNow());
    }

    /**
     * @expectedException \TypeError
     */
    public function testOffsetClockTypeError()
    {
        $fc = new FixedClock('1974-07-28 21:09');
        $c = new OffsetClock($fc, 12345678);  // 僅接受字串與 DateInterval
    }
}
