Change Log
==========

### 0.4.0 (2017-07-31)

- RouteLogger 增加 options 參數
    - 增加 ignoreMethods，忽略指定的 HTTP method

### 0.3.0 (2017-07-25)

- 新增 Configuration\GetEnv 類別
- 新增 DateTime\Clock 類別及 3 個具體時鐘類別：SystemClock、FixedClock 與 OffsetClock
- 新增 DateTime\Duration 類別
- Slim\Controller::checkParams() 增加檢查 PARAM_ARRAY

### 0.2.1 (2017-07-10)

- 改變 IpAddressFilter 建構子參數的順序，將 allowedIpAddresses 放到最後一個

### 0.2.0 (2017-07-10)

- 新增 Slim\Middleware\IpAddressFilter 類別

### 0.1.0 (2017-07-07)

- 提供「輪替式 access token 認證授權」網路設施，由下面的各類別構成
- 新增 Session\AccessTokenInfo、Session、SessionManager 類別
- 新增 Slim\Middleware 類別
- 新增 Slim\Middleware\SessionAuthorizer 類別
- 新增 Utils\Cache、Stringy 類別

### 0.0.6 (2017-07-04)

- 新增 Slim\Controller 與 ResponseHelperTrait 類別
- 新增 Error\BadRequest、ErrorCode 與 UserError 類別
- RouteLogger 可攔截 BadRequest 與 UserError 異常

### 0.0.5 (2017-07-03)

- 新增 Monolog\MonologFluentHandler 類別

### 0.0.4 (2017-06-21)

- 修正：要求 PHP 7.1 以上

### 0.0.3 (2017-06-21)

- 新增 Slim\Middleware\RouteLogger 與 ContainerIds 類別

### 0.0.2 (2017-06-19)

- LetterCase 類別移動到 Utils 命名空間
- 新增 LetterCase::kebabToPascal()

### 0.0.1 (2017-06-16)

- 初始版本
- 新增 LetterCase 類別
