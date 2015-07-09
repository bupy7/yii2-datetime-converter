<?php

namespace bupy7\datetime\translator;

use Yii;
use DateTime;
use DateTimeZone;
use yii\base\InvalidConfigException;
use yii\base\Component;

/**
 * Component for translate date and time for saving or display of user.
 * 
 * Usage:
 * 
 * Add component to your config: 
 * 
 * ~~~
 * 'dateTimeTranslator' => [
 *      'class' => 'bupy7\datetime\translator\Translator',
 *      // and config tranlsations if need for your locales (by default uses `en`)
 *      'ru' => [
            'displayTimeZone' => 'Europe/Moscow',
            'displayDate' => 'd.m.Y',
            'displayTime' => 'H:i:s',
            'displayDateTime' => 'd.m.Y, H:i:s'
        ],
 * ],
 * ~~~
 * 
 * @author Vasilij Belosludcev http://mihaly4.ru
 */
class Translator extends Component
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
     * @var array List of options translations between save date/time format and display date/time format.
     * Each element of array is language key with required properties for correcty translation operation.
     * Require properties:
     *      - `displayDateTime` - Date and time format for display of user.
     *      - `displayDate` - Date format for display of user.
     *      - `displayTime` - Time format for display of user.
     *      - `displayTimeZone` - Time zone for display of user.
     * @see http://php.net/manual/en/timezones.php
     * @see http://php.net/manual/ru/function.date.php
     */
    public $translations = [
        'en' => [
            'displayTimeZone' => 'UTC',
            'displayDate' => 'Y-m-d',
            'displayTime' => 'H:i:s',
            'displayDateTime' => 'Y-m-d, H:i:s'
        ],
    ];
    
    /**
     * Transalte date to saving.
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return string
     */
    public function toSaveDate($dt)
    {
        return $this->preSave($dt)->format($this->saveDate);
    }
    
    /**
     * Transalte date to display of user.
     * @param DateTime|string $dt Instance of DateTime or string with date. 
     * @return string 
     */
    public function toDisplayDate($dt)
    {
        return $this->preDisplay($dt)->format($this->displayDate);        
    }
    
    /**
     * Translate date and time to saving. 
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
     * Translate time to display of user.
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
     * Translate date and time to saving.
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
     * Transalte date and time to display of user.
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
     * Get property of current locale of application from translation settings.
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $locale = $this->getTranslation();
        if (isset($this->translations[$locale][$name])) {
            return $this->translations[$locale][$name];
        }
        return parent::__get($name);
    }
    
    /**
     * Return translation settings of current locale.
     * @return array
     * @throws InvalidConfigException
     */
    protected function getTranslation()
    {
        $locale = Yii::$app->language;
        if (!isset($this->translations[$locale])) {
            throw new InvalidConfigException("Locale key '{$locale}' not found in `\$translations`.");
        }
        return $locale;
    }
}
