<?php

namespace bupy7\date\translator;

use Yii;
use Carbon\Carbon;
use yii\base\InvalidConfigException;
use yii\base\Component;

class DateTranslator extends Component
{
    /**
     * @var string
     */
    public $saveTimeZone = 'UTC';
    /**
     * @var string
     */
    public $saveDate = 'Y-m-d';
    /**
     * @var string
     */
    public $saveTime = 'H:i:s';
    /**
     * @var string
     */
    public $saveDateTime = 'U';
    /**
     * @var array
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
     * @param Carbon|string $dt
     * @return string
     */
    public function toSaveDate($dt)
    {
        return $this->toSave($dt)->format($this->saveDate);
    }
    
    /**
     * 
     * @param Carbon|string $dt
     * @param string|null $locale
     * @return string
     */
    public function toDisplayDate($dt, $locale = null)
    {
        return $this->toDisplay($dt, $locale)->format($this->translators[$locale]['displayDate']);        
    }
    
    /**
     * 
     * @param Carbon|string $dt
     * @return string
     */
    public function toSaveTime($dt)
    {
        return $this->toSave($dt)->format($this->saveTime);
    }
    
    /**
     * 
     * @param Carbon|string $dt
     * @param string|null $locale
     * @return string
     */
    public function toDisplayTime($dt, $locale = null)
    {
        return $this->toDisplay($dt, $locale)->format($this->translators[$locale]['displayTime']);  
    }
    
    /**
     * 
     * @param Carbon|string $dt
     * @return string
     */
    public function toSaveDateTime($dt)
    {
        return $this->toSave($dt)->format($this->saveDateTime);
    }
    
    /**
     * 
     * @param Carbon|string $dt
     * @param string|null $locale
     * @return string
     */
    public function toDisplayDateTime($dt, $locale = null)
    {
        return $this->toDisplay($dt, $locale)->format($this->translators[$locale]['displayDateTime']); 
    }
    
    /**
     * 
     * @param Carbon|string $dt
     * @param string|null $locale
     * @return Carbon
     */
    protected function toDisplay($dt, $locale)
    {
        $locale = $this->getLocale($locale);
        if (!($dt instanceof Carbon)) {
            $dt = new Carbon($dt, $this->saveTimeZone);
        }
        $dt->setLocale($locale);
        return $dt->tz($this->translators[$locale]['displayTimeZone']);
    }
    
    /**
     * 
     * @param Carbon|string $dt
     * @return Carbon
     */
    protected function toSave($dt) 
    {
        $locale = $this->getLocale();
        if (!($dt instanceof Carbon)) {
            $dt = new Carbon($dt, $locale);
        }
        return $dt->tz($this->saveTimeZone);
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
