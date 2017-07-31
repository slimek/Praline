Documentation
=============

這個目錄是 Parline 程式庫的說明文件。

namespace Configuration
-----------------------

### GetEnv

讀取環境變數的便利函式，可以給預設值。


namespace DateTime
------------------

### Clock

提供可全域呼叫的時鐘。透過 `setCurrent()` 可指定三種不同的時鐘類型：

- `SystemClock`：系統時間，和 `new \DateTime()` 相同，也是 `Clock` 的預設行為。
- `FixedClock`：固定時間，在單元測試的時候很好用。
- `OffsetClock`：對參照的時鐘加上一個偏差值。在伺服器的測試與整備環境，可發揮類似「將系統時鐘調快一定時間」的效果，方便測試預定在未來觸發的事件而無需修改 DB 設定內容。

#### 使用範例

```php
use Praline\DateTime\Clock;
use Praline\DateTime\Clocks\{OffsetClock, SystemClock};

Clock::setCurrent(OffsetClock::byWeeks(new SystemClock(), 1));  // 時間往前調快 1 周
```

### Duration

擴充 `\DateInterval` 的功能，提供方便的建造函式，例如想要一段五分鐘的時間：

```php
use Praline\DateTime\Duration;

$fiveMinutes = Duration::minutes(5);
``` 


namespace Error
---------------

### BadRequest

表示客戶端發送要求的參數不合規格，間接表示客戶端程式可能有錯誤。

### ErrorCode

Praline 程式庫本身使用的錯誤代碼，數值皆為負數；應用程式的錯誤代碼請使用正數。

### UserError

表示使用者操作錯誤的異常類別。


namespace Monolog
-----------------

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


namespace Session
-----------------

### Access Token

傳給客戶端用以呼叫 API 的授權碼，由發行時間（精確度到微秒）及 session ID 組成，可轉換為 Base64 字串。

### Session

使用者身份認證後，保存其工作階段資訊的物件。

### SessionDataInterface

Session 可以保有一個應用提供的資料物件，此物件必須實作 SessionDataInterface 介面。

### SessionManager

Session 物件的管理元件，通常會作為 DI 服務物件而在 dependencies.php 中建立。 


namespace Slim
--------------

### Controller

Controllers 的基底類別，提供參數檢查與組裝回應的功能。

### Middleware

Middleware 的基底類別，提供異常攔截機制與歷程記錄用的 logger 。 

### ResponseHelperTrait

對 Slim 的 Response 提供一些輔助的功能，例如：
- 避免 UTF-8 字元被以 URL encode 處理。


namespace Slim\Middleware
-------------------------

### IpAddressFilter

除了白名單內的來源 IP 位址，其餘要求都以 403 拒絕。

### RouteLogger

可以加裝在 Route 上面的 Middleware，記錄每一次 API 呼叫的網址以及其回應 status code。

會攔截處理要求時擲出的各種異常，輸出適當的記錄並產生回應。

可作為 application middleware 來記錄所有的 API 呼叫：

```php
$container = $app->getContainer();
$app->add(new RouteLogger($container, [
    'ignoreMethods' => ['OPTIONS'],  // 忽略 CORS 的 OPTIONS 呼叫
]);    
```

### SessionAuthorizer

依照 Authorization header 裡面的 access token 來驗證使用者身份。

在產生回應時，無論成功或失敗都會替換下一次的 access token，並將回應記錄下來。為了做到這件事情，具有一套和 RouteLogger 相同的異常攔截機制。這機制攔截不到 SessionAuthorizor 在身份認證時期的異常 ～ 不過反正沒認證成功時，也沒必要把回應快取起來。 


namespace Utils
---------------

### Cache

將 [PSR-6](http://www.php-fig.org/psr/psr-6/) 的 cache 介面包裝成比較容易理解的 key-value 形式。

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

### Stringy

[Stringy](https://github.com/danielstjules/Stringy) 程式庫的擴充版本。
