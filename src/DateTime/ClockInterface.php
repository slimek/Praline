<?php
namespace Praline\DateTime;

// 可配置給 Clock 使用的時鐘介面
interface ClockInterface
{
    public function now(): \DateTime;
}
