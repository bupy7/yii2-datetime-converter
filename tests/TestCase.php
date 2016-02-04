<?php

namespace tests;

use Yii;
use PHPUnit_Framework_TestCase;
use yii\console\Application;

/**
 * This is the base class for all unit tests.
 * @inheritdoc
 * @author Belosludcev Vasilij <https://github.com/bupy7>
 * @since 1.1.1
 */
abstract class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var bupy7\datetime\converter\Converter
     */
    protected $dtConverter;
    
    
    /**
     * Mock application prior running tests.
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->mockApplication();
        $this->dtConverter = Yii::$app->get('dtConverter');
    }

    /**
     * Initialization mock application tests.
     */
    protected function mockApplication()
    {
        new Application([
            'id' => 'app-test',
            'timezone' => 'Europe/Moscow',
            'basePath' => __DIR__,
            'vendorPath' => __DIR__ . '/../vendor',
            'language' => 'ru',
            'components' => [
                'request' => [
                    'class' => 'yii\web\Request',
                    'url' => '/test',
                    'enableCsrfValidation' => false,
                ],
                'response' => [
                    'class' => 'yii\web\Response',
                ],
                'dtConverter' => [
                    'class' => 'bupy7\datetime\converter\Converter',
                    'patterns' => [
                        'ru' => [
                            'displayTimeZone' => 'Europe/Moscow',
                            'displayDate' => "'Custom text 'dd.LL.yyyy",
                            'displayTime' => 'php:H:i',
                            'displayDateTime' => 'php:d.m.Y, H:i',
                        ],
                    ],
                ],
            ],
        ]);
    }
    
    /**
     * Clean up after test.
     * By default the application created with mockApplication will be destroyed.
     * Destroys application in Yii::$app by setting it to null.
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();
        Yii::$app = null;
    }
}
