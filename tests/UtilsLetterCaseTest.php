<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Praline\Utils\LetterCase;

class UtilsLetterCaseTest extends TestCase
{
    public function testSplitFromKebab()
    {
        $this->assertEquals(
            [],
            LetterCase::split('', LetterCase::KEBAB)
        );

        $this->assertEquals(
            ['reimu'],
            LetterCase::split('reimu', LetterCase::KEBAB)
        );

        $this->assertEquals(
            ['alice', 'marisa'],
            LetterCase::split('alice-marisa', LetterCase::KEBAB)
        );

        // 雖然 kebab-case 不應該有大寫字，不過 split 並不會影響內容的大小寫
        $this->assertEquals(
            ['chen', 'Ran', 'YUKARI'],
            LetterCase::split('chen-Ran-YUKARI', LetterCase::KEBAB)
        );
    }

    public function testJoinToCamel()
    {
        $this->assertEquals(
            '',
            LetterCase::join([], LetterCase::CAMEL)
        );

        $this->assertEquals(
            'reimu',
            LetterCase::join(['reimu'], LetterCase::CAMEL)
        );

        $this->assertEquals(
            'aliceMarisa',
            LetterCase::join(['alice', 'marisa'], LetterCase::CAMEL)
        );
    }

    public function testKebabToCamel()
    {
        $this->assertEquals(
            '',
            LetterCase::kebabToCamel('')
        );

        $this->assertEquals(
            'reimu',
            LetterCase::kebabToCamel('reimu')
        );

        $this->assertEquals(
            'aliceMarisa',
            LetterCase::kebabToCamel('alice-marisa')
        );

        // 除了將 '-' 後面的字元轉成大寫，其他字元不做改變
        $this->assertEquals(
            'chenRanYUKARI',
            LetterCase::kebabToCamel('chen-Ran-YUKARI')
        );
    }

    public function testKebabToPascal()
    {
        $this->assertEquals(
            '',
            LetterCase::kebabToPascal('')
        );

        $this->assertEquals(
            'Reimu',
            LetterCase::kebabToPascal('reimu')
        );

        $this->assertEquals(
            'AliceMarisa',
            LetterCase::kebabToPascal('alice-marisa')
        );

        // 除了將 '-' 後面的字元轉成大寫，其他字元不做改變
        $this->assertEquals(
            'ChenRanYUKARI',
            LetterCase::kebabToPascal('chen-Ran-YUKARI')
        );
    }

    public function testSnakeToCamel()
    {
        $this->assertEquals(
            '',
            LetterCase::snakeToCamel('')
        );

        $this->assertEquals(
            'reimu',
            LetterCase::snakeToCamel('reimu')
        );

        $this->assertEquals(
            'aliceMarisa',
            LetterCase::snakeToCamel('alice_marisa')
        );

        // 除了將 '_' 後面的字元轉成大寫，其他字元不做改變
        $this->assertEquals(
            'chenRanYUKARI',
            LetterCase::snakeToCamel('chen_Ran_YUKARI')
        );

        // 已經是 camelCase 的必須不受影響
        $this->assertEquals(
            'remiliaSakuya',
            LetterCase::snakeToCamel('remiliaSakuya')
        );
    }

    public function testSnakeToCamelAllProperties()
    {
        $ori1 = (object)[
            'user_id' => 1234,
            'name' => 'Alice',
        ];

        $res1 = LetterCase::snakeToCamelAllProperties($ori1);

        $this->assertTrue(isset($res1->userId));
        $this->assertFalse(isset($res1->user_id));
        $this->assertTrue(isset($res1->name));
        $this->assertEquals(1234, $res1->userId);
        $this->assertEquals('Alice', $res1->name);

        // 空物件：會傳回一個新的空物件

        $ori2 = (object)[];

        $res2 = LetterCase::snakeToCamelAllProperties($ori2);

        $this->assertTrue($ori2 !== $res2);

        // 物件屬性的屬性不受影響；已是 camelCase 的也不受影響

        $ori3 = (object)[
            'name' => 'Marisa',
            'playerClass' => 'Witch',
            'has_iPhone' => false,
            'spell' => (object)[
                'spell_name' => 'Master Spark',
            ],
        ];

        $res3 = LetterCase::snakeToCamelAllProperties($ori3);

        $this->assertTrue(isset($res3->playerClass));
        $this->assertTrue(isset($res3->hasIPhone));
        $this->assertFalse(isset($res3->hasiPhone));
        $this->assertTrue(isset($res3->spell->spell_name));
        $this->assertFalse(isset($res3->spell->spellName));
    }
}
