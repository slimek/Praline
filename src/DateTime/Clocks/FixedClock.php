<?php
namespace Praline\DateTime\Clocks;

use Praline\DateTime\ClockInterface;

// 傳回一個固定時間，單元測試的時候可派上用場
class FixedClock implements ClockInterface
{
    /** @var  \DateTimeImmutable */
    private $fixedTime;

    public function __construct($fixedTime)
    {
        if (is_string($fixedTime)) {
            $this->fixedTime = new \DateTimeImmutable($fixedTime);
        } else if ($fixedTime instanceof \DateTimeInterface) {
            $dt = new \DateTimeImmutable();
            $dt = $dt->setTimestamp($fixedTime->getTimestamp());
            $dt = $dt->setTimezone($fixedTime->getTimezone());
            $this->fixedTime = $dt;
        } else {
            throw new \TypeError('Not a string or DateTime');
        }
    }

    public function now(): \DateTime
    {
        $dt = new \DateTime(null, $this->fixedTime->getTimeZone());
        return $dt->setTimestamp($this->fixedTime->getTimestamp());
    }
}
