<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Praline\Utils\Stringy as S;

class UtilsStringyTest extends TestCase
{
    public function testStringy()
    {
        $alice = S::create('Alice');

        $this->assertEquals('Alice', $alice->beforeFirst('M'));
        $this->assertEquals('Alice', $alice->beforeLast('M'));
        $this->assertEquals('', $alice->afterFirst('M'));
        $this->assertEquals('', $alice->afterLast('M'));

        $this->assertEquals('', $alice->beforeFirst('A'));
        $this->assertEquals('', $alice->beforeLast('A'));
        $this->assertEquals('lice', $alice->afterFirst('A'));
        $this->assertEquals('lice', $alice->afterLast('A'));

        $this->assertEquals('Alic', $alice->beforeFirst('e'));
        $this->assertEquals('Alic', $alice->beforeLast('e'));
        $this->assertEquals('', $alice->afterFirst('e'));
        $this->assertEquals('', $alice->afterLast('e'));

        $marisa = S::create('Marisa');

        $this->assertEquals('Ma', $marisa->beforeFirst('r'));
        $this->assertEquals('Ma', $marisa->beforeLast('r'));
        $this->assertEquals('isa', $marisa->afterFirst('r'));
        $this->assertEquals('isa', $marisa->afterLast('r'));

        $yuyuko = S::create('Yuyuko');

        $this->assertEquals('Y', $yuyuko->beforeFirst('u'));
        $this->assertEquals('Yuy', $yuyuko->beforeLast('u'));
        $this->assertEquals('yuko', $yuyuko->afterFirst('u'));
        $this->assertEquals('ko', $yuyuko->afterLast('u'));
    }

    // isBlank()
    // - 也可以同時檢查 null 與 false
    public function testStringyIsBlank()
    {
        $this->assertTrue(S::create('')->isBlank());
        $this->assertTrue(S::create('   ')->isBlank());
        $this->assertTrue(S::create("\n\t")->isBlank());
        $this->assertTrue(S::create(null)->isBlank());
        $this->assertTrue(S::create(false)->isBlank());

        // 下面的不是 blank
        $this->assertFalse(S::create(true)->isBlank());
        $this->assertFalse(S::create(0)->isBlank());

        // 下面的參數型別不被接受
        //$this->assertFalse(S::create([])->isBlank());
    }
}
