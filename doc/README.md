Documentation
=============

這個目錄是 Parline 程式庫的說明文件。

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
