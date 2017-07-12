<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Praline\Schedule\DurationFormatter;

class ScheduleDurationFormatterTest extends TestCase
{
    public function testDurationParse()
    {
        $this->assertDuration('P1W', '1w');
        $this->assertDuration('P7D', '7d');
        $this->assertDuration('P2W5D', '2w5d');
        $this->assertDuration('PT12H', '12h');
        $this->assertDuration('PT50M', '50m');
        $this->assertDuration('PT4H30M', '4h30m');
        $this->assertDuration('P1DT22H50M', '1d22h50m');
        $this->assertDuration('P3W6DT23H59M', '3w6d23h59m');

        // 長度 0 - 雖然沒用處，但還是可以產生
        $this->assertDuration('PT0S', '0d');
        $this->assertDuration('PT0S', '0m');
        $this->assertDuration('P5D', '5d0h0m');

        // 前後有空白會自動清除
        $this->assertDuration('PT7H30M', ' 7h30m ');

        // 不支援秒數
        $dur30s = DurationFormatter::tryParse('30s');
        $this->assertNull($dur30s);

        // 不支援負數
        $durN5h = DurationFormatter::tryParse('-5h');
        $this->assertNull($durN5h);

        // 其他不合法字串
        $durEmpty = DurationFormatter::tryParse('');
        $this->assertNull($durEmpty);
        $durBad = DurationFormatter::tryParse('bad');
        $this->assertNull($durBad);

        // 長度 0 會輸出為 0m
        $zero = new \DateInterval('P0D');
        $this->assertEquals('0m', DurationFormatter::format($zero));
    }

    private function assertDuration(string $interval, string $input)
    {
        $duration = DurationFormatter::tryParse($input);
        $this->assertEquals(new \DateInterval($interval), $duration);

        // DurationFormatter::format() 無法反過來產生有 w 單位的字串
        // 有 0 出現時，不一定能轉換回原版的輸入
        if (strpos($input, 'w') === false
         && strpos($input, '0') === false
        ) {
            $recovered = DurationFormatter::format($duration);
            $this->assertEquals($input, $recovered);
        }
    }
}