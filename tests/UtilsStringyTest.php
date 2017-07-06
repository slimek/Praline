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
}
