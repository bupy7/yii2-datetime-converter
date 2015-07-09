<?php

namespace bupy7\datetime\converter;

use Yii;
use DateTime;
use DateTimeZone;
use yii\base\InvalidConfigException;
use yii\base\Component;

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
 *              'displayDate' => 'd.m.Y',
 *              'displayTime' => 'H:i:s',
 *              'displayDateTime' => 'd.m.Y, H:i:s',
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
 * @author Vasilij Belosludcev http://mihaly4.ru
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
     * @var string Date format which uses for save in database.
     * @see http://php.net/manual/ru/function.date.php
     */
    public $saveDate = 'Y-m-d';
    /**
     * @var string Time format which uses for save in database.
     * @see http://php.net/manual/ru/function.date.php
     */
    public $saveTime = 'H:i:s';
    /**
     * @var string Date and time format which uses for save in database.
     * @see http://php.net/manual/ru/function.date.php
     */
    public $saveDateTime = 'U';
    /**
     * @var array List of formats date and time for display and saving.
     * Each element of array is language key with required properties for correcty converting operation.
     * Require properties:
     *      - `displayDateTime` - Date and time format for display of user.
     *      - `displayDate` - Date format for display of user.
     *      - `displayTime` - Time format for display of user.
     *      - `displayTimeZone` - Time zone for display of user.
     * You too can add any other properties at this array. 
     * @see http://php.net/manual/en/timezones.php
     * @see http://php.net/manual/ru/function.date.php
     */
    public $formats = [
        'en' => [
            'displayTimeZone' => 'UTC',
            'displayDate' => 'Y-m-d',
            'displayTime' => 'H:i:s',
            'displayDateTime' => 'Y-m-d, H:i:s'
        ],
    ];
    
    /**
     * Converting date to saving.
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return string
     */
    public function toSaveDate($dt)
    {
        return $this->preSave($dt)->format($this->saveDate);
    }
    
    /**
     * Converting date to display of user.
     * @param DateTime|string $dt Instance of DateTime or string with date. 
     * @return string 
     */
    public function toDisplayDate($dt)
    {
        return $this->preDisplay($dt)->format($this->displayDate);        
    }
    
    /**
     * Converting date and time to saving. 
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return string
     */
    public function toSaveTime($dt)
    {
        return $this->preSave($dt)
            ->setTimeZone(new DateTimeZone($this->saveTimeZone))
            ->format($this->saveTime);
    }
    
    /**
     * Converting time to display of user.
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return string
     */
    public function toDisplayTime($dt)
    {
        return $this->preDisplay($dt)
            ->setTimeZone(new DateTimeZone($this->displayTimeZone))
            ->format($this->displayTime);  
    }
    
    /**
     * Converting date and time to saving.
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return string
     */
    public function toSaveDateTime($dt)
    {
        return $this->preSave($dt)
            ->setTimeZone(new DateTimeZone($this->saveTimeZone))
            ->format($this->saveDateTime);
    }
    
    /**
     * Converting date and time to display of user.
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return string
     */
    public function toDisplayDateTime($dt)
    {
        return $this->preDisplay($dt)
            ->setTimeZone(new DateTimeZone($this->displayTimeZone))
            ->format($this->displayDateTime); 
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
        $locale = $this->getFormat();
        if (isset($this->format[$locale][$name])) {
            return $this->format[$locale][$name];
        }
        return parent::__get($name);
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
        return $locale;
    }
}
