<?php
namespace Praline\Session;

// SessionManager 授權用的 access token，由兩部份組成：
//   1. create time : 產生 access token 的時間
//   2. session ID : 由 SessionManager 產生的隨機字串
class AccessToken
{
    private const SEPARATOR = '&';

    /** @var  \DateTime */
    private $createTime;

    /** @var  string */
    private $sessionId;

    public static function createAtNow(string $sessionId): AccessToken
    {
        $token = new AccessToken();
        $token->createTime = new \DateTime();
        $token->sessionId = $sessionId;
        return $token;
    }

    // 從 base64 字串剖析 access token 的內容
    // 成功的話傳回 AccessToken 物件
    // 如果無法剖析，傳回 null
    public static function fromBase64(string $input): ?AccessToken
    {
        // 不是 base64 編碼
        $raw = base64_decode($input, true);
        if ($raw === false || $raw === '') {
            return null;
        }

        // 內容沒有 separator '&'
        $sepPos = strpos($raw, static::SEPARATOR);
        if ($sepPos === false) {
            return null;
        }

        $token = new AccessToken();

        // 不是合法的 DateTime 字串
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
