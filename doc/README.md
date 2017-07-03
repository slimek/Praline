Documentation
=============

這個目錄是 Parline 程式庫的說明文件。

Monolog
-------

### MonologFluentHandler

在透過 Monolog 輸出歷程到 Fluentd 的時候，預設的格式不是很漂亮，此處提供一個可讀性較高的輸出格式。

#### 使用範例

在 Slim 框架的 dependencies.php 之中：

```php
$container['logger'] = function ($container) {

    $logger = new Monolog\Logger('logger-channel');
    $handler = new Praline\Monolog\MonologFluentHandler('fluentd-host');
    $logger->pushHandler($handler);
    return $logger;
};
```

輸出的格式範例：

`2014-06-07 13:00:05 <tag>.INFO: Hello World! []`


Slim\Middleware
---------------

### RouteLogger

可以加裝在 Route 上面的 Middleware，記錄每一次 API 呼叫的網址以及其回應 status code。


Utils
----

### LetterCase

用於將一種 letter case 的字串轉換成另一種 letter case。

比方說可以利用 LetterCase 的函式產生出下面的程式碼：

```php
$app->post('/{controller}/{action}', function ($request, $response, $args) {

    $className = LetterCase::kebabToPascale($args['controller']) . 'Controller';
    $methodName = LetterCase::kebabToCamel($args['action']);
    
    $controller = new $className($this);
    return $controller->{$methodName}($request, $response);
});

```
