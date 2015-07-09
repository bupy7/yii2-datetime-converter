<?php

namespace bupy7\datetime\converter;

use Yii;
use DateTime;
use DateTimeZone;
use yii\base\InvalidConfigException;
use yii\base\Component;
use yii\helpers\FormatConverter;

/**
 * Component for converting date and time for saving or display of user.
 * 
 * Usage:
 * 
 * Add component to your config: 
 * 
 * ~~~
 * 'dtConverter' => [
 *      'class' => 'bupy7\datetime\converter\Converter',
 *      // add formats if need for your locales (by default uses `en`)
 *      'formats' => [
 *          'ru' => [
 *              'displayTimeZone' => 'Europe/Moscow',
 *              'displayDate' => 'php:d.m.Y',
 *              'displayTime' => 'php:H:i:s',
 *              'displayDateTime' => 'php:d.m.Y, H:i:s',
 *          ],
 *      ],
 * ],
 * ~~~
 * 
 * Examples:
 * 
 * ~~~
 * $datetime = 2015-06-07 12:45:00;
 * echo Yii::$app->dtConverter->toDisplayDateTime($datetime);
 * ~~~
 * or 
 * ~~~
 * $datetime = new DateTime('now');
 * echo Yii::$app->dtConverter->toDisplayDateTime($datetime);
 * ~~~
 * 
 * @author Belosludcev Vasilij <https://github.com/bupy7>
 * @since 1.0.0
 */
class Converter extends Component
{
    /**
     * @var string Time zone which uses for save in database.
     * @see http://php.net/manual/en/timezones.php
     */
    public $saveTimeZone = 'UTC';
    /**
     * @var string Date format which uses for save in database. (PHP or ICU format).
     * @see http://php.net/manual/ru/function.date.php
     * @see http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
     */
    public $saveDate = 'php:Y-m-d';
    /**
     * @var string Time format which uses for save in database. (PHP or ICU format).
     * @see http://php.net/manual/ru/function.date.php
     * @see http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
     */
    public $saveTime = 'php:H:i:s';
    /**
     * @var string Date and time format which uses for save in database. (PHP or ICU format).
     * @see http://php.net/manual/ru/function.date.php
     * @see http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
     */
    public $saveDateTime = 'php:U';
    /**
     * @var array List of formats date and time for display and saving.
     * 
     * Each element of array is language key with required formats for correcty converting operation:
     *      - `displayTimeZone` - Time zone for display of user.
     *      - `displayDateTime` - Date and time format for display of user. (PHP or ICU format).
     *      - `displayDate` - Date format for display of user. (PHP or ICU format).
     *      - `displayTime` - Time format for display of user. (PHP or ICU format).
     * 
     * You too can add any other formats at this array. 
     * 
     * @see http://php.net/manual/en/timezones.php
     * @see http://php.net/manual/ru/function.date.php
     * @see http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
     */
    public $formats = [
        'en' => [
            'displayTimeZone' => 'UTC',
            'displayDate' => 'php:Y-m-d',
            'displayTime' => 'php:H:i:s',
            'displayDateTime' => 'php:Y-m-d, H:i:s'
        ],
    ];
    
    /**
     * Converting date to saving.
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return string
     */
    public function toSaveDate($dt)
    {
        $format = self::normalizeFormat($this->saveDate, 'date');
        return $this->preSave($dt)->format($format);
    }
    
    /**
     * Converting date to display of user.
     * @param DateTime|string $dt Instance of DateTime or string with date. 
     * @return string 
     */
    public function toDisplayDate($dt)
    {
        $format = self::normalizeFormat($this->displayDate, 'date');
        return $this->preDisplay($dt)->format($format);        
    }
    
    /**
     * Converting date and time to saving. 
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return string
     */
    public function toSaveTime($dt)
    {
        $format = self::normalizeFormat($this->saveTime, 'time');
        return $this->preSave($dt)
            ->setTimeZone(new DateTimeZone($this->saveTimeZone))
            ->format($format);
    }
    
    /**
     * Converting time to display of user.
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return string
     */
    public function toDisplayTime($dt)
    {
        $format = self::normalizeFormat($this->displayTime, 'time');
        return $this->preDisplay($dt)
            ->setTimeZone(new DateTimeZone($this->displayTimeZone))
            ->format($format);  
    }
    
    /**
     * Converting date and time to saving.
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return string
     */
    public function toSaveDateTime($dt)
    {
        $format = self::normalizeFormat($this->saveDateTime, 'datetime');
        return $this->preSave($dt)
            ->setTimeZone(new DateTimeZone($this->saveTimeZone))
            ->format($format);
    }
    
    /**
     * Converting date and time to display of user.
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return string
     */
    public function toDisplayDateTime($dt)
    {
        $format = self::normalizeFormat($this->displayDateTime, 'datetime');
        return $this->preDisplay($dt)
            ->setTimeZone(new DateTimeZone($this->displayTimeZone))
            ->format($format); 
    }
    
    /**
     * Return instance of DateTime with time zone of saving or copy $dt class.
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return DateTime
     * @throws Exception
     */
    public function preDisplay($dt)
    {
        if (!($dt instanceof DateTime)) {
            $dt = new DateTime($dt, new DateTimeZone($this->saveTimeZone));
        } else {
            $dt = clone $dt;
        }
        return $dt;
    }
    
    /**
     * Return instance of DateTime with time zone of display or copy $dt class.
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return DateTime
     * @throws Exception
     */
    public function preSave($dt) 
    {
        if (!($dt instanceof DateTime)) {
            $dt = new DateTime($dt, new DateTimeZone($this->displayTimeZone));
        } else {
            $dt = clone $dt;
        }
        return $dt;
    }
    
    /**
     * Get the value of format of current locale of application from formats settings.
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $format = $this->getFormat();
        if (isset($format[$name])) {
            return $format[$name];
        }
        return parent::__get($name);
    }
    
    /**
     * Normalize a date format pattern from ICU format to php date() function format.
     *
     * The conversion is limited to date patterns that do not use escaped characters.
     * Patterns like `d 'of' MMMM yyyy` which will result in a date like `1 of December 2014` may not be 
     * converted correctly because of the use of escaped characters.
     *
     * Pattern constructs that are not supported by the PHP format will be removed.
     *
     * php date() function format: http://php.net/manual/en/function.date.php
     * ICU format: http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
     * 
     * @param string $pattern date format pattern in ICU format.
     * @param string $type 'date', 'time', or 'datetime'.
     * @return string Normalize date format pattern.
     */
    static public function normalizeFormat($pattern, $type = 'date')
    {
        if (strpos($pattern, 'php:') === 0) {
            return substr($pattern, 4);         
        } 
        return FormatConverter::convertDateIcuToPhp($pattern, $type);
    }
    
    /**
     * Return format settings of current locale.
     * @return array
     * @throws InvalidConfigException
     */
    protected function getFormat()
    {
        $locale = Yii::$app->language;
        if (!isset($this->formats[$locale])) {
            throw new InvalidConfigException("Locale key '{$locale}' not found in `\$formats`.");
        }
        return $this->formats[$locale];
    }
}
