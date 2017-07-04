<?php
namespace Praline\Error;

// UserError 代表的是「由玩家行為造成的失敗狀況」，例如玩家輸入錯誤的帳號密碼之類的。
// 在 RouteLogger 裡面會產生適當的 response 回應給 client，但不會輸出多餘的錯誤訊息到 log。
class UserError extends \Exception
{
    /** @var  int */
    private $httpStatusCode;

    public function __construct(int $httpStatusCode, string $message, int $code)
    {
        parent::__construct($message, $code);

        $this->httpStatusCode = $httpStatusCode;
    }

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }
}
