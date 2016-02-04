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
    public function testToSaveDate()
    {
        $this->assertEquals('2016-12-12', $this->dtConverter->toSaveDate('12.12.2016 00:00:00'));
        $this->assertEquals('2016-12-11', $this->dtConverter->toSaveDate('12.12.2016 00:00:00', true));
        $dt = Carbon::now($this->dtConverter->displayTimeZone);
        $dt->tz($this->dtConverter->saveTimeZone);
        $this->assertEquals($dt->toDateString(), $this->dtConverter->toSaveDate($dt));
        $this->assertEquals($dt->toDateString(), $this->dtConverter->toSaveDate($dt, true));
    }
    
    public function testToDisplayDate()
    {
        $this->assertEquals('Custom text 12.12.2016', $this->dtConverter->toDisplayDate('2016-12-12 00:00:00'));
        $this->assertEquals('Custom text 12.12.2016', $this->dtConverter->toDisplayDate('2016-12-12 20:00:00', true));
        $dt = Carbon::now($this->dtConverter->saveTimeZone);
        $dt->tz($this->dtConverter->displayTimeZone);
        $this->assertEquals('Custom text ' . $dt->format('d.m.Y'), $this->dtConverter->toDisplayDate($dt));
        $this->assertEquals('Custom text ' . $dt->format('d.m.Y'), $this->dtConverter->toDisplayDate($dt), true);
    }
    
    public function testToSaveTime()
    {
        $this->assertEquals('00:00:00', $this->dtConverter->toSaveTime('12.12.2016 00:00:00'));
        $this->assertEquals('21:00:00', $this->dtConverter->toSaveTime('12.12.2016 00:00:00', true));
        $dt = Carbon::now($this->dtConverter->displayTimeZone);
        $dt->tz($this->dtConverter->saveTimeZone);
        $this->assertEquals($dt->toTimeString(), $this->dtConverter->toSaveTime($dt));
        $this->assertEquals($dt->toTimeString(), $this->dtConverter->toSaveTime($dt, true));
    }
    
    public function testToDisplayTime()
    {
        $this->assertEquals('00:00', $this->dtConverter->toDisplayTime('2016-12-12 00:00:00'));
        $this->assertEquals('00:00', $this->dtConverter->toDisplayTime('2016-12-12 21:00:00', true));
        $dt = Carbon::now($this->dtConverter->saveTimeZone);
        $dt->tz($this->dtConverter->displayTimeZone);
        $this->assertEquals($dt->format('H:i'), $this->dtConverter->toDisplayTime($dt));
        $this->assertEquals($dt->format('H:i'), $this->dtConverter->toDisplayTime($dt), true);
    }
    
    public function testToSaveDateTime()
    {
        $this->assertEquals('1481490000', $this->dtConverter->toSaveDateTime('12.12.2016 00:00:00'));
        $this->assertEquals('1481490000', $this->dtConverter->toSaveDateTime('1481490000'));
        $dt = Carbon::now($this->dtConverter->displayTimeZone);
        $dt->tz($this->dtConverter->saveTimeZone);
        $this->assertEquals($dt->format('U'), $this->dtConverter->toSaveDateTime($dt));
    }
    
    public function testToDisplayDateTime()
    {
        $this->assertEquals('12.12.2016, 00:00', $this->dtConverter->toDisplayDateTime('1481490000'));
        $dt = Carbon::now($this->dtConverter->saveTimeZone);
        $dt->tz($this->dtConverter->displayTimeZone);
        $this->assertEquals($dt->format('d.m.Y, H:i'), $this->dtConverter->toDisplayDateTime($dt));
    }
    
    /**
     * @expectedException yii\base\UnknownPropertyException
     */
    public function testInvalidProperty()
    {
        $this->dtConverter->invalidProperty;
    }
    
    /**
     * @expectedException yii\base\InvalidConfigException
     */
    public function testInvalidFormatPattern()
    {
        Yii::$app->language = 'en';
        $this->dtConverter->toDisplayDateTime('1481490000');
    }
}

