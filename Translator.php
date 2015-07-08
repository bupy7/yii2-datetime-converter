<?php

namespace bupy7\date\translator;

use Yii;
use DateTime;
use DateTimeZone;
use yii\base\InvalidConfigException;
use yii\base\Component;

/**
 * Component for translate dates from one format/time zone to other.
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
    public $translators = [
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
        return $this->toSave($dt)->format($this->saveDate);
    }
    
    /**
     * Transalte date to display of user.
     * @param DateTime|string $dt Instance of DateTime or string with date. 
     * @return string 
     */
    public function toDisplayDate($dt)
    {
        $locale = $this->getTranslation();
        return $this->toDisplay($dt)->format($this->translators[$locale]['displayDate']);        
    }
    
    /**
     * Translate date and time to saving. 
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return string
     */
    public function toSaveTime($dt)
    {
        return $this->toSave($dt)->format($this->saveTime);
    }
    
    /**
     * Translate time to display of user.
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return string
     */
    public function toDisplayTime($dt)
    {
        $locale = $this->getTranslation();
        return $this->toDisplay($dt)->format($this->translators[$locale]['displayTime']);  
    }
    
    /**
     * Translate date and time to saving.
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return string
     */
    public function toSaveDateTime($dt)
    {
        return $this->toSave($dt)->format($this->saveDateTime);
    }
    
    /**
     * Transalte date and time to display of user.
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return string
     */
    public function toDisplayDateTime($dt)
    {
        $locale = $this->getTranslation();
        return $this->toDisplay($dt)->format($this->translators[$locale]['displayDateTime']); 
    }
    
    /**
     * Return instance of DateTime with settings for display of user.
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return DateTime
     * @throws Exception
     */
    public function toDisplay($dt)
    {
        $locale = $this->getTranslation();
        if (!($dt instanceof DateTime)) {
            $dt = new DateTime($dt, new DateTimeZone($this->saveTimeZone));
        } else {
            $dt = clone $dt;
        }
        return $dt->setTimeZone(new DateTimeZone($this->translators[$locale]['displayTimeZone']));
    }
    
    /**
     * Return instance of DateTime with settings for save to database.
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return DateTime
     * @throws Exception
     */
    public function toSave($dt) 
    {
        $locale = $this->getTranslation();
        if (!($dt instanceof DateTime)) {
            $dt = new DateTime($dt, new DateTimeZone($this->translators[$locale]['displayTimeZone']));
        } else {
            $dt = clone $dt;
        }
        return $dt->setTimeZone(new DateTimeZone($this->saveTimeZone));
    }
    
    /**
     * Get property of current locale of application from translation settings.
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $locale = $this->getTranslation();
        if (isset($this->translators[$locale][$name])) {
            return $this->translators[$locale][$name];
        }
        return parent::__get($name);
    }
    
    /**
     * Return translation settings of current/custom locale.
     * @param string|null $locale
     * @return array
     * @throws InvalidConfigException
     */
    protected function getTranslation($locale = null)
    {
        if ($locale == null) {
            $locale = Yii::$app->language;
        }
        if (!isset($this->translators[$locale])) {
            throw new InvalidConfigException("Locale key '{$locale}' not found in `\$translators`.");
        }
        return $locale;
    }
}
