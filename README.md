yii2-datetime-converter
====================

Converting date/time from display/save to save/display format.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist bupy7/yii2-datetime-converter "*"
```

or add

```
"bupy7/yii2-datetime-converter": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Add component to your config: 
 
```php
'dtConverter' => [
    'class' => 'bupy7\datetime\converter\Converter',
    // 'saveTimeZone' => 'UTC' - by default
    // 'saveDate' => 'php:Y-m-d' - by default
    // 'saveTime' => 'php:H:i:s' - by default
    // 'saveDateTime' => 'php:U' - by default
    // add format patterns if need for your locales (by default uses `en`)
    'patterns' => [
        'ru' => [
            'displayTimeZone' => 'Europe/Moscow',
            'displayDate' => 'php:d.m.Y',
            'displayTime' => 'php:H:i',
            'displayDateTime' => 'php:d.m.Y, H:i',
        ],
    ],
],
```

```php
$datetime = 2015-06-07 12:45:00;
echo Yii::$app->dtConverter->toDisplayDateTime($datetime);
```
or 
```php
$datetime = new DateTime('now');
echo Yii::$app->dtConverter->toDisplayDateTime($datetime);
```

You can add behavior of your model for converting date/time before save.

```php
use bupy7\datetime\converter\ConverterBehavior;

public function behaviors()
{
    return [
        // converter date/time before save
        [
            'class' => ConverterBehavior::className(),
            'type' => ConverterBehavior::TYPE_DATE_TIME,
            'to' => ConverterBehavior::TO_SAVE,
            'attributes' => [
                self::EVENT_BEFORE_SAVE => ['attribute_1', 'attribute_2'],
            ],
        ],
    ];
}
```

##License

yii2-grid is released under the BSD 3-Clause License.
