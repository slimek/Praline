<?php
namespace Praline\DateTime\Clocks;

use Praline\DateTime\ClockInterface;

// 單純地傳回系統時鐘
class SystemClock implements ClockInterface
{
    public function now(): \DateTime
    {
        return new \DateTime();
    }
}
