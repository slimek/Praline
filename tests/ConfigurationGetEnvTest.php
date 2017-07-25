<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Praline\Configuration\GetEnv;

class ConfigurationGetEnvTest extends TestCase
{
    public function testGetEnvBool()
    {
        $this->assertNull(GetEnv::bool('PRALINE_TEST_FLAG'));
        $this->assertTrue(GetEnv::bool('PRALINE_TEST_FLAG', true));

        putenv('PRALINE_TEST_FLAG=true');
        $this->assertTrue(GetEnv::bool('PRALINE_TEST_FLAG'));

        putenv('PRALINE_TEST_FLAG=false');
        $this->assertFalse(GetEnv::bool('PRALINE_TEST_FLAG'));

        putenv('PRALINE_TEST_FLAG=1');
        $this->assertTrue(GetEnv::bool('PRALINE_TEST_FLAG'));

        putenv('PRALINE_TEST_FLAG=0');
        $this->assertFalse(GetEnv::bool('PRALINE_TEST_FLAG'));

        putenv('PRALINE_TEST_FLAG=yes');
        $this->assertTrue(GetEnv::bool('PRALINE_TEST_FLAG'));

        putenv('PRALINE_TEST_FLAG=no');
        $this->assertFalse(GetEnv::bool('PRALINE_TEST_FLAG'));

        putenv('PRALINE_TEST_FLAG=on');
        $this->assertTrue(GetEnv::bool('PRALINE_TEST_FLAG'));

        putenv('PRALINE_TEST_FLAG=off');
        $this->assertFalse(GetEnv::bool('PRALINE_TEST_FLAG'));

        // 不區分大小寫

        putenv('PRALINE_TEST_FLAG=Yes');
        $this->assertTrue(GetEnv::bool('PRALINE_TEST_FLAG'));

        putenv('PRALINE_TEST_FLAG=NO');
        $this->assertFalse(GetEnv::bool('PRALINE_TEST_FLAG'));

        // 無效數值

        putenv('PRALINE_TEST_FLAG=2');
        $this->assertNull(GetEnv::bool('PRALINE_TEST_FLAG'));

        putenv('PRALINE_TEST_FLAG=unknown');
        $this->assertNull(GetEnv::bool('PRALINE_TEST_FLAG'));
    }

    public function testGetEnvString()
    {
        $this->assertNull(GetEnv::string('PRALINE_TEST_TEXT'));
        $this->assertEquals('Never mind!', GetEnv::string('PRALINE_TEST_TEXT', 'Never mind!'));

        putenv('PRALINE_TEST_TEXT=Hello world!');

        $this->assertEquals('Hello world!', GetEnv::string('PRALINE_TEST_TEXT'));
    }
}
