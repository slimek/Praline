<?php
namespace Tests\Common;

// 如果測試過程中需要一些代表資料的小類別，可以用這個
class BookInfo
{
    /** @var string  */
    private $title;

    /** @var string  */
    private $author;

    public function __construct(string $title, string $author)
    {
        $this->title = $title;
        $this->author = $author;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }
}
