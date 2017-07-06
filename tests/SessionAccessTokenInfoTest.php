<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Praline\Session\AccessTokenInfo;

class SessionAccessTokenTest extends TestCase
{
    public function testAccessToken()
    {
        $token2 = AccessTokenInfo::createAtNow('dummy-session-id');
        $b64Token2 = $token2->toBase64();
        $token2Rev = AccessTokenInfo::fromBase64($b64Token2);

        $this->assertEquals($token2->getCreateTime(), $token2Rev->getCreateTime());
        $this->assertEquals($token2->getSessionId(), $token2Rev->getSessionId());

        $b64Token3 = base64_encode('1998-12-05T08:40:30.235800&dummy-session-id');
        $token3 = AccessTokenInfo::fromBase64($b64Token3);

        $this->assertEquals(new \DateTime('1998-12-05 08:40:30.235800'), $token3->getCreateTime());
        $this->assertEquals('dummy-session-id', $token3->getSessionId());

        // 不合法的 base64 access token 要傳回 null

        // 不是 base64 編碼
        $notBase64 = AccessTokenInfo::fromBase64('A.&*/$');
        $this->assertNull($notBase64);

        // 沒有 separator '&'
        $noSeparator = AccessTokenInfo::fromBase64('2014-06-18T05:45:17.187539');
        $this->assertNull($noSeparator);

        // 前半段不是合法的 DateTime 字串
        $notDateTime = AccessTokenInfo::fromBase64('123685914&dummy-session-id');
        $this->assertNull($notDateTime);
    }
}
