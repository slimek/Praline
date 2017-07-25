<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Praline\DateTime\Duration;

class DateTimeDurationTest extends TestCase
{
    /** @var  \DateTimeImmutable */
    private $basis;

    public function testDuration()
    {
        $this->basis = new \DateTimeImmutable('2017-04-05 16:30');

        $this->assertDuration('2017-04-05 16:30:00', Duration::zero());

        $this->assertDuration('2017-04-05 16:30:00', Duration::seconds(0));
        $this->assertDuration('2017-04-05 16:30:05', Duration::seconds(5));
        $this->assertDuration('2017-04-05 16:29:45', Duration::seconds(-15));

        $this->assertDuration('2017-04-05 16:37:00', Duration::minutes(7));
        $this->assertDuration('2017-04-05 15:58:00', Duration::minutes(-32));

        $this->assertDuration('2017-04-05 19:30:00', Duration::hours(3));
        $this->assertDuration('2017-04-05 04:30:00', Duration::hours(-12));

        $this->assertDuration('2017-04-07 16:30:00', Duration::days(2));
        $this->assertDuration('2017-03-30 16:30:00', Duration::days(-6));

        $this->assertDuration('2017-05-03 16:30:00', Duration::weeks(4));
        $this->assertDuration('2017-03-15 16:30:00', Duration::weeks(-3));
    }

    private function assertDuration(string $dateTime, Duration $interval)
    {
        $this->assertEquals(new \DateTimeImmutable($dateTime), $this->basis->add($interval));
    }
}
