<?php
namespace Praline\Utils;

// 基於 danielstjules/stringy 的 Stringy 類別，再加上自己所需的擴充函式
// 本類別實作 __toString()，因此通常可自動轉換為 string，但有時你會需要用 strval() 來強制轉型
//
// 推薦使用方式：
//
//   use Praline\Utils\Stringy as S;
//
//   $result = S::create('text')->toUpperCase();
//
class Stringy extends \Stringy\Stringy
{
    // Before 系列
    // - 如果沒找到 separator，傳回整個字串

    public function beforeFirst(string $separator): Stringy
    {
        $p = $this->indexOf($separator);
        return $p === false ? $this : $this->first($p);
    }

    public function beforeLast(string $separator): Stringy
    {
        $p = $this->indexOfLast($separator);
        return $p === false ? $this : $this->first($p);
    }

    // After 系列
    // - 如果沒找到 separator，傳回空字串

    public function afterFirst(string $separator): Stringy
    {
        $p = $this->indexOf($separator);
        return $p === false ? static::create('')
                            : $this->substr($p + static::create($separator)->length());
    }

    public function afterLast(string $separator): Stringy
    {
        $p = $this->indexOfLast($separator);
        return $p === false ? static::create('')
                            : $this->substr($p + static::create($separator)->length());
    }
}
