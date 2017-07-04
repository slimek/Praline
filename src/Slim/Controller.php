<?php
namespace Praline\Slim;

use Praline\Error\{BadRequest, ErrorCode};

// 用於 Slim Framework 之中，各 Controller 的基底類別
class Controller
{
    use ResponseHelperTrait;

    // 參數型別
    const PARAM_STRING = 'string';
    const PARAM_NUMBER = 'number';
    const PARAM_OBJECT = 'object';

    // 檢查必需的參數是否存在、以及型別是否符合預期
    // - 假定全部 POST API 都要求用 JSON 格式的 body 來傳遞參數，
    //   此處的 params 應該傳入 request->getParsedBody() 的傳回值。
    //   每個參數都必須指定上面的參數型別
    protected function checkParams(array $params, array $paramSpecs)
    {
        foreach ($paramSpecs as $name => $type) {
            if (!array_key_exists($name, $params)) {
                throw new BadRequest("Missing param '$name'", ErrorCode::MISSING_PARAMETER);
            }

            $value = $params[$name];

            switch ($type) {
                case static::PARAM_STRING:
                    if (!is_string($value)) {
                        throw new BadRequest(
                            "Invalid $name: '$value', should be a string",
                            ErrorCode::INVALID_PARAMETER
                        );
                    }
                    break;
                case static::PARAM_NUMBER:
                    if (!is_numeric($value)) {
                        throw new BadRequest(
                            "Invalid $name: '$value', should be a number",
                            ErrorCode::INVALID_PARAMETER
                        );
                    }
                    break;
                case static::PARAM_OBJECT:
                    // 無法針對物件的內容檢查，先跳過
                    break;
                default:
                    throw new BadRequest(
                        "Parameter $name should be given a type",
                        ErrorCode::INVALID_PARAMETER
                    );
            }
        }
    }
}
