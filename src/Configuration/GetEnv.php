<?php
namespace Praline\Configuration;

// 讀取環境變數的輔助元件
class GetEnv
{
    // 讀取環境變數並當作 boolean 來解釋
    // - 使用 filter_var(FILTER_VALIDATE_BOOLEAN) 來剖析，
    //   視為 true：true、1、on、yes
    //   視為 false：false、0、off、no （無視大小寫）
    //   其餘的剖析失敗一律傳回 null
    //   找不到環境變數且沒有預設值，也是傳回 null
    public static function bool(string $valueName, bool $defaultValue = null): ?bool
    {
        $value = getenv($valueName, true) ?: getenv($valueName);

        if ($value === false) {
            return $defaultValue;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    // 讀取環境變數並當作 int 來解釋
    public static function int(string $valueName, int $defaultValue = null): ?int
    {
        $value = getenv($valueName, true) ?: getenv($valueName);

        if ($value === false) {
            return $defaultValue;
        }

        return filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
    }

    // 讀取環境變數並直接當作 string 來解釋
    //   找不到環境變數且沒有預設值，傳回 null
    public static function string(string $valueName, string $defaultValue = null): ?string
    {
        $value = getenv($valueName, true) ?: getenv($valueName);

        if ($value === false) {
            return $defaultValue;
        } else {
            return $value;
        }
    }
}
