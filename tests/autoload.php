<?php

require __DIR__ . '/../vendor/autoload.php';

// 將 tests/Common 目錄下的類別加入 autoload 機制中
spl_autoload_register(function ($className) {

    if (strncmp('Tests\\Common', $className, 12) !== 0) {
        return;
    }

    $filePath = '/Common/' . substr($className, 13);
    require __DIR__ . $filePath . '.php';
});
