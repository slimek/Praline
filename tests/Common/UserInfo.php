<?php
namespace Tests\Common;

use Praline\Session\SessionDataInterface;

// 如果測試過程中需要一些代表資料的小類別，可以用這個
class UserInfo implements SessionDataInterface
{
    /** @var  int */
    public $id;

    /** @var  string */
    public $name;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getUniqueKey(): string
    {
        return strval($this->id);
    }

    public function getDescription(): string
    {
        return 'userId(' . strval($this->id) . ')';
    }
}
