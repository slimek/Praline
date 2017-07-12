<?php
namespace Praline\Schedule;

use Praline\Utils\Stringy as S;

// 時間長度字串的格式化
// 格式：
//   w 週
//   d 天
//   h 小時
//   m 分鐘
// 範例：
//   1w6d - 1 週 6 天
//   22h  - 22 小時
class DurationFormatter
{
    public static function tryParse(string $input): ?\DateInterval
    {
        $input = strval(S::create($input)->trim());

        if (empty($input)) {
            return null;
        }

        $dur = 'P';

        $matches = [];
        if (preg_match('/^(\d+)w(.*)/', $input, $matches) === 1) {
            $dur .= $matches[1] . 'W';
            $input = $matches[2];
        }
        if (preg_match('/^(\d+)d(.*)/', $input, $matches) === 1) {
            $dur .= $matches[1] . 'D';
            $input = $matches[2];
        }
        if (preg_match('/^(\d+)h(.*)/', $input, $matches) === 1) {
            $dur .= 'T' . $matches[1] . 'H';
            $input = $matches[2];
        }
        if (preg_match('/^(\d+)m(.*)/', $input, $matches) === 1) {
            if (strpos($dur, 'T') === false) {
                $dur .= 'T';
            }
            $dur .= $matches[1] . 'M';
            $input = $matches[2];
        }
        if (!empty($input)) {
            return null;
        }

        return new \DateInterval($dur);
    }

    // 如果有無法表示的屬性，會擲出異常
    // 注意：這個函式無法產生出以週為單位的字串
    public static function format(\DateInterval $duration): string
    {
        if ($duration->y !== 0
         || $duration->m !== 0
         || $duration->s !== 0
        ) {
          throw new \Exception('Unsupported components for a Schedule Duration');
        }

        $output = '';
        if ($duration->d !== 0) {
            $output .= $duration->d . 'd';
        }
        if ($duration->h !== 0) {
            $output .= $duration->h . 'h';
        }
        if ($duration->i !== 0) {
            $output .= $duration->i . 'm';
        }

        // 長度 0 的情況
        if ($output === '') {
            $output = '0m';
        }

        return $output;
    }
}
