<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Praline\DateTime\Clock;
use Praline\DateTime\Clocks\FixedClock;

class DateTimeClocksFixedClockTest extends TestCase
{
    public function testFixedClock()
    {
        $c1 = new FixedClock('2017-04-05 16:30');
        $this->assertEquals(new \DateTime('2017-04-05 16:30'), $c1->now());

        $t2 = new \DateTime('1974-07-28 21:09');
        $c2 = new FixedClock($t2);
        $this->assertEquals(new \DateTime('1974-07-28 21:09'), $c2->now());

        // 配置給全域 Clock

        Clock::setCurrent($c1);
        $this->assertEquals(new \DateTime('2017-04-05 16:30'), Clock::now());
        $this->assertEquals('2017-04-05 16:30:00', Clock::dbNow());
    }

    /**
     * @expectedException \TypeError
     */
    public function testFixedClockTypeError()
    {
        $c = new FixedClock(12345678);  // 僅接受字串與 DateTime
    }
}
