<?php
namespace Praline\Session;


class Session
{
    private const ID_BYTES = 12;  // Session ID 的 bytes 數

    // 12 bytes 的隨機 ID 會不會發生碰撞呢？別擔心，SessionManager 在新增 session 時會檢查 id 是否重覆
    // 為什麼是 12 而不是 8 或 16 ？這是為了讓 base64 編碼的 access token 不要出現後面的 '=' 字元

    /** @var  string - Hex 字串﹐長度是 ID_BYTES 的 2 倍 */
    private $sessionId;

    /** @var  string - Base64 字串，預期下一次要求必須使用的 access token */
    private $nextAccessToken;

    /** @var  SessionDataInterface - Session 附帶的資料，隨應用而不同 */
    private $data;

    /** @var  string - Base64 字串，這個要求已經完成回應，如果接到相同 access token 的要求，可以直接將快取回應傳回 */
    private $cachedAccessToken;

    /** @var  string - JSON 字串，前一次回應的快取 */
    private $cachedResponseJson;

    public function __construct(SessionDataInterface $data)
    {
        $this->sessionId = bin2hex(openssl_random_pseudo_bytes(static::ID_BYTES));
        $this->nextAccessToken = AccessTokenInfo::createAtNow($this->sessionId)->toBase64();
        $this->data = $data;
        $this->cachedAccessToken = null;
        $this->cachedResponseJson = null;
    }

    public function getId(): string
    {
        return $this->sessionId;
    }

    public function getNextAccessToken(): string
    {
        return $this->nextAccessToken;
    }

    public function getData(): SessionDataInterface
    {
        return $this->data;
    }

    // 如果尚無快取的資料，會傳回 null
    public function getCachedAccessToken(): ?string
    {
        return $this->cachedAccessToken;
    }

    // 如果尚無快取的資料，會傳回 null
    public function getCachedResponseJson(): ?string
    {
        return $this->cachedResponseJson;
    }

    // 比對 base64 編碼的 access token，只要 cached 或 next 其中一個符合即可
    public function matchAccessToken(string $base64AccessToken): bool
    {
        return $this->nextAccessToken === $base64AccessToken
            || $this->cachedAccessToken === $base64AccessToken;
    }

    // 產生新的 access token 並且向前替換，同時清除 cachedResponseJson
    public function advanceAccessToken(): string
    {
        $this->cachedAccessToken = $this->nextAccessToken;
        $this->nextAccessToken = AccessTokenInfo::createAtNow($this->sessionId)->toBase64();
        $this->cachedResponseJson = null;
        return $this->nextAccessToken;
    }

    public function setResponseJson(string $responseJson)
    {
        $this->cachedResponseJson = $responseJson;
    }
}
