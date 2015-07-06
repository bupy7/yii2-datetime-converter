<?php

namespace bupy7\date\translator;

use Yii;
use DateTime;
use DateTimeZone;
use yii\base\InvalidConfigException;
use yii\base\Component;

/**
 * 
 * @author Vasilij Belosludcev http://mihaly4.ru
 */
class DateTranslator extends Component
{
    /**
     * @var string
     * @see http://php.net/manual/en/timezones.php
     */
    public $saveTimeZone = 'UTC';
    /**
     * @var string
     * @see http://php.net/manual/ru/function.date.php
     */
    public $saveDate = 'Y-m-d';
    /**
     * @var string
     * @see http://php.net/manual/ru/function.date.php
     */
    public $saveTime = 'H:i:s';
    /**
     * @var string
     * @see http://php.net/manual/ru/function.date.php
     */
    public $saveDateTime = 'U';
    /**
     * @var array
     * @see http://php.net/manual/en/timezones.php
     * @see http://php.net/manual/ru/function.date.php
     */
    public $translators = [
        'ru' => [
            'displayTimeZone' => 'Europe/Minsk',
            'displayDate' => 'd.m.Y',
            'displayTime' => 'H:i:s',
            'displayDateTime' => 'd.m.Y, H:i:s'
        ],
    ];
    
    /**
     * 
     * @param DateTime|string $dt
     * @return string
     */
    public function toSaveDate($dt)
    {
        return $this->toSave($dt)->format($this->saveDate);
    }
    
    /**
     * 
     * @param DateTime|string $dt
     * @param string|null $locale
     * @return string
     */
    public function toDisplayDate($dt, $locale = null)
    {
        $locale = $this->getLocale($locale);
        return $this->toDisplay($dt, $locale)->format($this->translators[$locale]['displayDate']);        
    }
    
    /**
     * 
     * @param DateTime|string $dt
     * @return string
     */
    public function toSaveTime($dt)
    {
        return $this->toSave($dt)->format($this->saveTime);
    }
    
    /**
     * 
     * @param DateTime|string $dt
     * @param string|null $locale
     * @return string
     */
    public function toDisplayTime($dt, $locale = null)
    {
        $locale = $this->getLocale($locale);
        return $this->toDisplay($dt, $locale)->format($this->translators[$locale]['displayTime']);  
    }
    
    /**
     * 
     * @param DateTime|string $dt
     * @return string
     */
    public function toSaveDateTime($dt)
    {
        return $this->toSave($dt)->format($this->saveDateTime);
    }
    
    /**
     * 
     * @param DateTime|string $dt
     * @param string|null $locale
     * @return string
     */
    public function toDisplayDateTime($dt, $locale = null)
    {
        $locale = $this->getLocale($locale);
        return $this->toDisplay($dt, $locale)->format($this->translators[$locale]['displayDateTime']); 
    }
    
    /**
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $locale = $this->getLocale();
        if (isset($this->translators[$locale][$name])) {
            return $this->translators[$locale][$name];
        }
        return parent::__get($name);
    }
    
    /**
     * 
     * @param DateTime|string $dt
     * @param string|null $locale
     * @return DateTime
     * @throws Exception
     */
    protected function toDisplay($dt, $locale)
    {
        if (!($dt instanceof DateTime)) {
            $dt = new DateTime($dt, new DateTimeZone($this->saveTimeZone));
        } else {
            $dt = clone $dt;
        }
        return $dt->setTimeZone($this->translators[$locale]['displayTimeZone']);
    }
    
    /**
     * 
     * @param DateTime|string $dt
     * @return DateTime
     * @throws Exception
     */
    protected function toSave($dt) 
    {
        $locale = $this->getLocale();
        if (!($dt instanceof DateTime)) {
            $dt = new DateTime($dt, new DateTimeZone($this->translators[$locale]['displayTimeZone']));
        } else {
            $dt = clone $dt;
        }
        return $dt->setTimeZone($this->saveTimeZone);
    }
    
    /**
     * 
     * @param string|null $locale
     * @return string
     * @throws InvalidConfigException
     */
    protected function getLocale($locale = null)
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
