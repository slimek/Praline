<?php
namespace Praline\Slim;

// 用於 Slim Framework 之中，各 Middleware 的基底類別
class Middleware
{
    use ResponseHelperTrait;

    // 產生一個代表錯誤狀況的資料，可以直接用於產生 response
    protected function makeErrorResult(int $errorCode, string $message)
    {
        return [
            'error' => [
                'code' => $errorCode,
                'message' => $message,
            ],
        ];
    }

    // 從 Throwable 異常物件產生出一個代表錯誤狀況的資料，可以直接用於產生 response
    // 通常我們會直接使用 Throwable 的 code
    // 不過也可以用 replaceErrorCode 將錯誤代碼替換掉
    protected function makeResultFromThrowable(\Throwable $t, $replaceErrorCode = null): array
    {
        return [
            'error' => [
                'code' => $replaceErrorCode ?? $t->getCode(),
                'message' => $t->getMessage(),
            ],
        ];
    }
}
