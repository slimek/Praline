<?php
namespace Praline\Error;

// BadRequest 代表的是「Client 送來的資料不正確」，而且通常是由程式自身的錯誤造成的。
// 在 RouteLogger 之中回應的 HTTP status code 一律都是 401
class BadRequest extends \Exception
{
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}
