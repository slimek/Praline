<?php

require __DIR__ . '/../vendor/autoload.php';

// 將 src/Utils 目錄下的類別加入 autoload 機制中
spl_autoload_register(function ($className) {

    if (strncmp('Utils', $className, 5) !== 0) {
        return;
    }

    $filePath = str_replace('\\', '/', $className);
    require __DIR__ . '/../src/' . $filePath . '.php';
});
