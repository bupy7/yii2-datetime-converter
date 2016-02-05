<?php

namespace tests;

use Yii;
use PHPUnit_Framework_TestCase;

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
    static protected $dtConverter;   
    
    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {      
        self::$dtConverter = Yii::$app->get('dtConverter');
    }
}
