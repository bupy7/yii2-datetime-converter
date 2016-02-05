<?php

namespace tests;

use Yii;
use Carbon\Carbon;

/**
 * Unit testing of `bupy7\bupy7\datetime\converter\Converter` class.
 * @author Belosludcev Vasilij <https://github.com/bupy7>
 * @since 1.1.1
 */
class ConverterTest extends TestCase
{    
    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->setLanguage('ru');
        self::$dtConverter->patterns = [
            'ru' => [
                'displayTimeZone' => 'Europe/Moscow',
                'displayDate' => "'Custom text 'dd.LL.yyyy",
                'displayTime' => 'php:H:i',
                'displayDateTime' => 'php:d.m.Y, H:i',
            ],
        ];
    }
    
    public function testToSaveDate()
    {
        $this->assertEquals('2016-12-12', self::$dtConverter->toSaveDate('12.12.2016 00:00:00'));
        $this->assertEquals('2016-12-11', self::$dtConverter->toSaveDate('12.12.2016 00:00:00', true));
        $dt = Carbon::now(self::$dtConverter->displayTimeZone);
        $dt->tz(self::$dtConverter->saveTimeZone);
        $this->assertEquals($dt->toDateString(), self::$dtConverter->toSaveDate($dt));
        $this->assertEquals($dt->toDateString(), self::$dtConverter->toSaveDate($dt, true));
    }
    
    public function testToDisplayDate()
    {
        $this->assertEquals('Custom text 12.12.2016', self::$dtConverter->toDisplayDate('2016-12-12 00:00:00'));
        $this->assertEquals('Custom text 12.12.2016', self::$dtConverter->toDisplayDate('2016-12-12 20:00:00', true));
        $dt = Carbon::now(self::$dtConverter->saveTimeZone);
        $dt->tz(self::$dtConverter->displayTimeZone);
        $this->assertEquals('Custom text ' . $dt->format('d.m.Y'), self::$dtConverter->toDisplayDate($dt));
        $this->assertEquals('Custom text ' . $dt->format('d.m.Y'), self::$dtConverter->toDisplayDate($dt), true);
    }
    
    public function testToSaveTime()
    {
        $this->assertEquals('00:00:00', self::$dtConverter->toSaveTime('12.12.2016 00:00:00'));
        $this->assertEquals('21:00:00', self::$dtConverter->toSaveTime('12.12.2016 00:00:00', true));
        $dt = Carbon::now(self::$dtConverter->displayTimeZone);
        $dt->tz(self::$dtConverter->saveTimeZone);
        $this->assertEquals($dt->toTimeString(), self::$dtConverter->toSaveTime($dt));
        $this->assertEquals($dt->toTimeString(), self::$dtConverter->toSaveTime($dt, true));
    }
    
    public function testToDisplayTime()
    {
        $this->assertEquals('00:00', self::$dtConverter->toDisplayTime('2016-12-12 00:00:00'));
        $this->assertEquals('00:00', self::$dtConverter->toDisplayTime('2016-12-12 21:00:00', true));
        $dt = Carbon::now(self::$dtConverter->saveTimeZone);
        $dt->tz(self::$dtConverter->displayTimeZone);
        $this->assertEquals($dt->format('H:i'), self::$dtConverter->toDisplayTime($dt));
        $this->assertEquals($dt->format('H:i'), self::$dtConverter->toDisplayTime($dt), true);
    }
    
    public function testToSaveDateTime()
    {
        $this->assertEquals('1481490000', self::$dtConverter->toSaveDateTime('12.12.2016 00:00:00'));
        $this->assertEquals('1481490000', self::$dtConverter->toSaveDateTime('1481490000'));
        $dt = Carbon::now(self::$dtConverter->displayTimeZone);
        $dt->tz(self::$dtConverter->saveTimeZone);
        $this->assertEquals($dt->format('U'), self::$dtConverter->toSaveDateTime($dt));
    }
    
    public function testToDisplayDateTime()
    {
        $this->assertEquals('12.12.2016, 00:00', self::$dtConverter->toDisplayDateTime('1481490000'));
        $dt = Carbon::now(self::$dtConverter->saveTimeZone);
        $dt->tz(self::$dtConverter->displayTimeZone);
        $this->assertEquals($dt->format('d.m.Y, H:i'), self::$dtConverter->toDisplayDateTime($dt));
    }
    
    /**
     * @expectedException yii\base\UnknownPropertyException
     */
    public function testInvalidProperty()
    {
        self::$dtConverter->invalidProperty;
    }
    
    /**
     * @expectedException yii\base\InvalidConfigException
     */
    public function testInvalidFormatPattern()
    {
        $this->setLanguage('en');
        self::$dtConverter->toDisplayDateTime('1481490000');
    }
}

