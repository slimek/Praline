<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Praline\Slim\Controller;

class SlimControllerTest extends TestCase
{
    public function testCheckParams()
    {
        $controller = new ErsatzController();

        $controller->applyTest();

        // 這個測試只是確認函式可正常呼叫，因此補上一個代用的 assertion 以避免警告
        $this->assertTrue(true);
    }
}

// 由於 checkParams() 是 protected 函式，此處臨時衍生一個 Controller 類別
class ErsatzController extends Controller
{
    public function applyTest()
    {
        $params = [
            'intValue' => 123,
            'floatValue' => 456.798,
            'stringValue' => 'Fantasy',
            'objectValue' => (object)[
                'name' => 'Marisa',
                'spells' => ['Master Spark', 'Blazing Start'],
            ],
            'arrayValue' => [
                'Easy', 'Normal', 'Hard', 'Lunatic',
            ],
        ];

        $this->checkParams($params, [
            'intValue'    => Controller::PARAM_NUMBER,
            'floatValue'  => Controller::PARAM_NUMBER,
            'stringValue' => Controller::PARAM_STRING,
            'objectValue' => Controller::PARAM_OBJECT,
            'arrayValue'  => Controller::PARAM_ARRAY,
        ]);
    }
}