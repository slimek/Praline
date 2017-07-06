<?php
namespace Praline\Session;

// 這是 access token 的輔助應用元件
//
// Praline 的 access token 為 base64 編碼的字串，其中原始資料由兩部份組成：
//   1. create time : 產生 access token 的時間
//   2. session ID : 由 SessionManager 產生的隨機字串
//
// AccessTokenInfo 提供的功能為：
//   1. 由上述兩個成份來產生 access token
//   2. 檢查 access token 是否符合 Praline 的規格
//   3. 從 access token 中將上述兩個成份剖析出來
//
class AccessTokenInfo
{
    private const SEPARATOR = '&';

    /** @var  \DateTime */
    private $createTime;

    /** @var  string */
    private $sessionId;

    public static function createAtNow(string $sessionId): AccessTokenInfo
    {
        $token = new AccessTokenInfo();
        $token->createTime = new \DateTime();
        $token->sessionId = $sessionId;
        return $token;
    }

    // 從 base64 字串剖析 access token 的內容
    // 成功的話傳回 AccessTokenInfo 物件
    // 如果無法剖析，傳回 null
    public static function fromBase64(string $input): ?AccessTokenInfo
    {
        // 是否為 base64 編碼
        $raw = base64_decode($input, true);
        if ($raw === false || $raw === '') {
            return null;
        }

        // 檢查內容沒有 separator '&'
        $sepPos = strpos($raw, static::SEPARATOR);
        if ($sepPos === false) {
            return null;
        }

        $token = new AccessTokenInfo();

        // 是否為合法的 DateTime 字串
        try {
            $token->createTime = new \DateTime(substr($raw, 0, $sepPos));
        } catch (\Throwable $t) {
            return null;
        }

        $token->sessionId = substr($raw, $sepPos + 1);

        return $token;
    }

    public function toBase64(): string
    {
        return base64_encode($this->createTime->format("Y-m-d\TH:i:s.u") . static::SEPARATOR . $this->sessionId);
    }

    public function getCreateTime(): \DateTime
    {
        return $this->createTime;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }
}
