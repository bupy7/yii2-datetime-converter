<?php

namespace tests;

use Yii;
use bupy7\datetime\converter\ConverterBehavior;
use tests\models\Post;
use bupy7\datetime\converter\Converter;

/**
 * Unit testing of `bupy7\bupy7\datetime\converter\ConverterBehavior` class.
 * @author Belosludcev Vasilij <https://github.com/bupy7>
 * @since 1.1.1
 */
class ConvertBehaviorTest extends TestCase
{
    /**
     * @inheritdoc
     */
    public function setUp()
    {
        self::$dtConverter->patterns = [
            'ru' => [
                'displayTimeZone' => 'Europe/Moscow',
                'displayDate' => 'php:d.m.Y',
                'displayTime' => 'php:H:i',
                'displayDateTime' => 'php:d.m.Y, H:i',
            ],
        ];
    }
    
    public function testTimeConverting()
    {
        $model = new Post(self::$dtConverter);
        $model->attachBehaviors([
            [
                'class' => ConverterBehavior::className(),
                'type' => ConverterBehavior::TYPE_TIME,
                'to' => ConverterBehavior::TO_SAVE,
                'attributes' => [
                    Post::EVENT_BEFORE_VALIDATE => ['time'],
                ],
            ],
            [
                'class' => ConverterBehavior::className(),
                'type' => ConverterBehavior::TYPE_TIME,
                'to' => ConverterBehavior::TO_DISPLAY,
                'attributes' => [
                    Post::EVENT_AFTER_VALIDATE => ['time'],
                ],
            ],
        ]);
        $model->load(['time' => '11:25'], '');
        if ($model->validate()) {
            $this->assertEquals('11:25:00', $model->getPreparedData()['time']);
            $this->assertEquals('11:25', $model->time);
        } else {
            $this->fail(implode(PHP_EOL, $model->getErrors('time')));
        }
    }
    
    public function testDateConverting()
    {
        $model = new Post(self::$dtConverter);
        $model->attachBehaviors([
            [
                'class' => ConverterBehavior::className(),
                'type' => ConverterBehavior::TYPE_DATE,
                'to' => ConverterBehavior::TO_SAVE,
                'attributes' => [
                    Post::EVENT_BEFORE_VALIDATE => ['date'],
                ],
            ],
            [
                'class' => ConverterBehavior::className(),
                'type' => ConverterBehavior::TYPE_DATE,
                'to' => ConverterBehavior::TO_DISPLAY,
                'attributes' => [
                    Post::EVENT_AFTER_VALIDATE => ['date'],
                ],
            ],
        ]);
        $model->load(['date' => '12.12.2016'], '');
        if ($model->validate()) {
            $this->assertEquals('2016-12-12', $model->getPreparedData()['date']);
            $this->assertEquals('12.12.2016', $model->date);
        } else {
            $this->fail(implode(PHP_EOL, $model->getErrors('date')));
        }
    }
    
    public function testDateTimeConverting()
    {
        $model = new Post(self::$dtConverter);
        $model->attachBehaviors([
            [
                'class' => ConverterBehavior::className(),
                'type' => ConverterBehavior::TYPE_DATE_TIME,
                'to' => ConverterBehavior::TO_SAVE,
                'attributes' => [
                    Post::EVENT_BEFORE_VALIDATE => ['datetime'],
                ],
            ],
            [
                'class' => ConverterBehavior::className(),
                'type' => ConverterBehavior::TYPE_DATE_TIME,
                'to' => ConverterBehavior::TO_DISPLAY,
                'attributes' => [
                    Post::EVENT_AFTER_VALIDATE => ['datetime'],
                ],
            ],
        ]);
        $model->load(['datetime' => '12.12.2016, 11:25'], '');
        if ($model->validate()) {
            $this->assertEquals('1481531100', $model->getPreparedData()['datetime']);
            $this->assertEquals('12.12.2016, 11:25', $model->datetime);
        } else {
            $this->fail(implode(PHP_EOL, $model->getErrors('datetime')));
        }
    }
    
    public function testArrayConfigConverter()
    {
        $model = new Post(self::$dtConverter);
        $model->attachBehaviors([
            [
                'class' => ConverterBehavior::className(),
                'type' => ConverterBehavior::TYPE_DATE_TIME,
                'to' => ConverterBehavior::TO_SAVE,
                'attributes' => [
                    Post::EVENT_BEFORE_VALIDATE => ['datetime'],
                ],
                'converter' => [
                    'class' => Converter::className(),
                    'patterns' => self::$dtConverter->patterns,
                ],
            ],
            [
                'class' => ConverterBehavior::className(),
                'type' => ConverterBehavior::TYPE_DATE_TIME,
                'to' => ConverterBehavior::TO_DISPLAY,
                'attributes' => [
                    Post::EVENT_AFTER_VALIDATE => ['datetime'],
                ],
            ],
        ]);
        $model->load(['datetime' => '12.12.2016, 11:25'], '');
        if ($model->validate()) {
            $this->assertEquals('1481531100', $model->getPreparedData()['datetime']);
            $this->assertEquals('12.12.2016, 11:25', $model->datetime);
        } else {
            $this->fail(implode(PHP_EOL, $model->getErrors('datetime')));
        }
    }
    
    /**
     * @expectedException yii\base\InvalidConfigException
     */
    public function testInstanceConfigConverter()
    {
        $model = new Post(self::$dtConverter);
        $model->attachBehaviors([
            [
                'class' => ConverterBehavior::className(),
                'type' => ConverterBehavior::TYPE_DATE_TIME,
                'to' => ConverterBehavior::TO_SAVE,
                'attributes' => [
                    Post::EVENT_BEFORE_VALIDATE => ['datetime'],
                ],
                'converter' => Yii::$app->get('formatter'),
            ],
        ]);
    }
    
    /**
     * @expectedException yii\base\InvalidConfigException
     */
    public function testInvalidTypeConfigBehavior1()
    {
        $model = new Post(self::$dtConverter);
        $model->attachBehaviors([
            [
                'class' => ConverterBehavior::className(),
                'to' => ConverterBehavior::TO_SAVE,
                'attributes' => [
                    Post::EVENT_BEFORE_VALIDATE => ['datetime'],
                ],
            ],
        ]);
    }
    
    /**
     * @expectedException yii\base\InvalidConfigException
     */
    public function testInvalidTypeConfigBehavior2()
    {
        $model = new Post(self::$dtConverter);
        $model->attachBehaviors([
            [
                'class' => ConverterBehavior::className(),
                'to' => 3,
                'attributes' => [
                    Post::EVENT_BEFORE_VALIDATE => ['datetime'],
                ],
            ],
        ]);
    }
    
    /**
     * @expectedException yii\base\InvalidConfigException
     */
    public function testInvalidToConfigBehavior1()
    {
        $model = new Post(self::$dtConverter);
        $model->attachBehaviors([
            [
                'class' => ConverterBehavior::className(),
                'type' => ConverterBehavior::TYPE_DATE_TIME,
                'attributes' => [
                    Post::EVENT_BEFORE_VALIDATE => ['datetime'],
                ],
            ],
        ]);
    }
    
    /**
     * @expectedException yii\base\InvalidConfigException
     */
    public function testInvalidToConfigBehavior2()
    {
        $model = new Post(self::$dtConverter);
        $model->attachBehaviors([
            [
                'class' => ConverterBehavior::className(),
                'type' => 4,
                'attributes' => [
                    Post::EVENT_BEFORE_VALIDATE => ['datetime'],
                ],
            ],
        ]);
    }
}
