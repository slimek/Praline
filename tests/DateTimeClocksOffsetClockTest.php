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

        $c3 = OffsetClock::byWeeks($fc, 4);
        $this->assertEquals(new \DateTime('2017-05-03 16:30'), $c3->now());

        $c4 = OffsetClock::byDays($fc, 15);
        $this->assertEquals(new \DateTime('2017-04-20 16:30'), $c4->now());

        $c5 = OffsetClock::byDays($fc, -7);
        $this->assertEquals(new \DateTime('2017-03-29 16:30'), $c5->now());


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
