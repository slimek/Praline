<?php
namespace Praline\Slim;

// Composer
use Slim\Http\Response;

// 給 Slim 的 Response 補充一些功能
// 這個 Trait 可供各 Controllers 與各種 Middleware 共同使用（透過基底類別 Controller 及 Middleware 引入）
trait ResponseHelperTrait
{
    // Slim Response::withJson() 內部使用 json_encode() 來產生 JSON 資料，
    // 但預設會將 UTF-8 編碼轉為 escaped 字串，因此我們必須額外增加 JSON_UNESCAPED_UNICODE 旗標。
    // 使用這個 trait 函式可讓程式碼簡潔些。
    // 使用方式：
    //   將原本的
    //     return $response->withJson($result);
    //   改為
    //     return $this->withJson($response, $result);
    protected function withJson(Response $response, $result, int $statusCode = 200): Response
    {
        return $response->withJson(
            $result,
            $statusCode,
            \JSON_UNESCAPED_UNICODE
        );
    }
}
