<?php
namespace Praline\Monolog;

use Fluent\Logger\Entity;
use Fluent\Logger\FluentLogger;
use Monolog\Handler\AbstractProcessingHandler;

class MonologFluentHandler extends AbstractProcessingHandler
{
    /** @var FluentLogger */
    private $logger;

    public function __construct(
        $host = FluentLogger::DEFAULT_ADDRESS,
        $port = FluentLogger::DEFAULT_LISTEN_PORT
    ) {
        parent::__construct();  // level 與 bubble 參數都使用預設值

        $this->logger = new FluentLogger($host, $port);
    }

    // 輸出範例：
    // 2014-06-07 13:00:05 penny.INFO: Hello World! []
    // - 其中時間部分由 fluentd.conf 決定，無法在 Handler 裡控制
    public function write(array $record)
    {
        $channel = $record['channel'] ?? '_tag_';

        $tag = $channel . '.' . $record['level_name'];
        $data = $record['message'];

        $this->logger->post2(new Entity($tag, $data));
    }
}
