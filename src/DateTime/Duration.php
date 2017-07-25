<?php
namespace Praline\DateTime;

// 表示一段時間長度
class Duration extends \DateInterval
{
    public function __construct($duration)
    {
        parent::__construct($duration);
    }

    public static function zero()
    {
        return new Duration('P0D');
    }

    public static function weeks(int $weeks): Duration
    {
        if ($weeks >= 0) {
            return new Duration('P' . strval($weeks) . 'W');
        } else {
            $dur = new Duration('P' . strval(-$weeks) . 'W');
            $dur->invert = 1;
            return $dur;
        }
    }

    public static function days(int $days): Duration
    {
        if ($days >= 0) {
            return new Duration('P' . strval($days) . 'D');
        } else {
            $dur = new Duration('P' . strval(-$days) . 'D');
            $dur->invert = 1;
            return $dur;
        }

    }

    public static function hours(int $hours): Duration
    {
        if ($hours >= 0) {
            return new Duration('PT' . strval($hours) . 'H');
        } else {
            $dur = new Duration('PT' . strval(-$hours) . 'H');
            $dur->invert = 1;
            return $dur;
        }
    }

    public static function minutes(int $minutes): Duration
    {
        if ($minutes >= 0) {
            return new Duration('PT' . strval($minutes) . 'M');
        } else {
            $dur = new Duration('PT' . strval(-$minutes) . 'M');
            $dur->invert = 1;
            return $dur;
        }
    }

    public static function seconds(int $seconds): Duration
    {
        if ($seconds >= 0) {
            return new Duration('PT' . strval($seconds) . 'S');
        } else {
            $dur = new Duration('PT' . strval(-$seconds) . 'S');
            $dur->invert = 1;
            return $dur;
        }
    }
}
