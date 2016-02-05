<?php

namespace tests;

use Yii;
use bupy7\datetime\converter\ConverterBehavior;
use tests\models\Post;

/**
 * Unit testing of `bupy7\bupy7\datetime\converter\ConverterBehavior` class.
 * @author Belosludcev Vasilij <https://github.com/bupy7>
 * @since 1.1.1
 */
class ConvertBehaviorTest extends TestCase
{
    public function testConverting()
    {
        $model = Post;
        $model->attachBehaviors([
            [
                'class' => ConverterBehavior::className(),
                'type' => ConverterBehavior::TYPE_DATE_TIME,
                'to' => ConverterBehavior::TO_SAVE,
                'attributes' => [
                    Post::EVENT_BEFORE_VALIDATE => ['created_at'],
                ],
            ],
            [
                'class' => ConverterBehavior::className(),
                'type' => ConverterBehavior::TYPE_DATE_TIME,
                'to' => ConverterBehavior::TO_DISPLAY,
                'attributes' => [
                    Post::EVENT_AFTER_VALIDATE => ['created_at'],
                ],
            ],
        ]);
    }
}
