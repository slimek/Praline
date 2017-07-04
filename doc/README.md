Documentation
=============

這個目錄是 Parline 程式庫的說明文件。


Error
-----

### BadRequest

表示客戶端發送要求的參數不合規格，間接表示客戶端程式可能有錯誤。

### ErrorCode

Praline 程式庫本身使用的錯誤代碼，數值皆為負數；應用程式的錯誤代碼請使用正數。

### UserError

表示使用者操作錯誤的異常類別。


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


Slim
----

### Controller

Controllers 的基底類別，提供參數檢查與組裝回應的功能。

### ResponseHelperTrait

對 Slim 的 Response 提供一些輔助的功能，例如：
- 避免 UTF-8 字元被以 URL encode 處理。


Slim\Middleware
---------------

### RouteLogger

可以加裝在 Route 上面的 Middleware，記錄每一次 API 呼叫的網址以及其回應 status code。

會攔截處理要求時擲出的各種異常，輸出適當的記錄並產生回應。


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
