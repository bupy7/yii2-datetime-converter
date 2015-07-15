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
 *      // add format patterns if need for your locales (by default uses `en`)
 *      'patterns' => [
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
     * @var string Date format which uses for save in database. (php date function or ICU format pattern).
     * @see http://php.net/manual/ru/function.date.php
     * @see http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
     */
    public $saveDate = 'php:Y-m-d';
    /**
     * @var string Time format which uses for save in database. (php date function or ICU format pattern).
     * @see http://php.net/manual/ru/function.date.php
     * @see http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
     */
    public $saveTime = 'php:H:i:s';
    /**
     * @var string Date and time format which uses for save in database. (php date function or ICU format pattern).
     * @see http://php.net/manual/ru/function.date.php
     * @see http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
     */
    public $saveDateTime = 'php:U';
    /**
     * @var array List of format patterns date and time for display and saving.
     * 
     * Each element of array is language key with required format patterns for correcty converting operation:
     *      - `displayTimeZone` - Time zone for display of user.
     *      - `displayDateTime` - Date and time format for display of user. (php date function or ICU format pattern).
     *      - `displayDate` - Date format for display of user. (php date function or ICU format pattern).
     *      - `displayTime` - Time format for display of user. (php date function or ICU format pattern).
     * 
     * You too can add any other format patterns at this array. 
     * 
     * @see http://php.net/manual/en/timezones.php
     * @see http://php.net/manual/ru/function.date.php
     * @see http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
     */
    public $patterns = [
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
     * @param boolean $useTimeZone Whether set `true` then will be uses time zone for saving.
     * @return string
     */
    public function toSaveDate($dt, $useTimeZone = false)
    {
        $pattern = self::normalizePattern($this->saveDate);
        $result = $this->preSave($dt);
        if ($useTimeZone) {
            $result->setTimeZone(new DateTimeZone($this->saveTimeZone));
        }
        return $result->format($pattern);
    }
    
    /**
     * Converting date to display of user.
     * @param DateTime|string $dt Instance of DateTime or string with date. 
     * @param boolean $useTimeZone Whether set `true` then will be uses time zone for saving.
     * @return string 
     */
    public function toDisplayDate($dt, $useTimeZone = false)
    {
        $pattern = self::normalizePattern($this->displayDate);
        $result = $this->preDisplay($dt);
        if ($useTimeZone) {
            $result->setTimeZone(new DateTimeZone($this->displayTimeZone));
        }
        return $result->format($pattern);      
    }
    
    /**
     * Converting date and time to saving. 
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @param boolean $useTimeZone Whether set `true` then will be uses time zone for saving.
     * @return string
     */
    public function toSaveTime($dt, $useTimeZone = false)
    {
        $pattern = self::normalizePattern($this->saveTime);
        $result = $this->preSave($dt);
        if ($useTimeZone) {
            $result->setTimeZone(new DateTimeZone($this->saveTimeZone));
        }
        return $result->format($pattern);
    }
    
    /**
     * Converting time to display of user.
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @param boolean $useTimeZone Whether set `true` then will be uses time zone for saving.
     * @return string
     */
    public function toDisplayTime($dt, $useTimeZone = false)
    {
        $pattern = self::normalizePattern($this->displayTime);
        $result = $this->preDisplay($dt);
        if ($useTimeZone) {
            $result->setTimeZone(new DateTimeZone($this->displayTimeZone));
        }
        return $result->format($pattern);  
    }
    
    /**
     * Converting date and time to saving.
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return string
     */
    public function toSaveDateTime($dt)
    {
        $pattern = self::normalizePattern($this->saveDateTime);
        return $this->preSave($dt)
            ->setTimeZone(new DateTimeZone($this->saveTimeZone))
            ->format($pattern);
    }
    
    /**
     * Converting date and time to display of user.
     * @param DateTime|string $dt Instance of DateTime or string with date.
     * @return string
     */
    public function toDisplayDateTime($dt)
    {
        $pattern = self::normalizePattern($this->displayDateTime);
        return $this->preDisplay($dt)
            ->setTimeZone(new DateTimeZone($this->displayTimeZone))
            ->format($pattern); 
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
            if (is_numeric($dt)) {
                $dt = '@' . $dt;
            }
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
     * Get the value of format pattern of current locale of application.
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $pattern = $this->getPattern();
        if (isset($pattern[$name])) {
            return $pattern[$name];
        }
        return parent::__get($name);
    }
    
    /**
     * Normalize a date format pattern for apply to date/time class and functions.
     *
     * If format pattern is ICU then this will be normalize to php date function format.
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
     * @return string Normalize date format pattern.
     */
    static public function normalizePattern($pattern)
    {
        if (strpos($pattern, 'php:') === 0) {
            return substr($pattern, 4);         
        } 
        // http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
        // escaped text
        $escaped = [];
        $matches = [];
        if (preg_match_all('/(?<!\')\'(.*?[^\'])\'(?!\')/', $pattern, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $match[1] = str_replace('\'\'', '\'', $match[1]);
                $escaped[$match[0]] = '\\'.implode('\\', preg_split('//u', $match[1], -1, PREG_SPLIT_NO_EMPTY));
            }
        }
        return strtr($pattern, array_merge($escaped, [
            '\'\'' => '\\\'', // two single quotes produce one
            'G' => '', // era designator like (Anno Domini)
            'Y' => 'o',     // 4digit year of "Week of Year"
            'y' => 'Y',     // 4digit year e.g. 2014
            'yyyy' => 'Y',  // 4digit year e.g. 2014
            'yy' => 'y',    // 2digit year number eg. 14
            'u' => '',      // extended year e.g. 4601
            'U' => '',      // cyclic year name, as in Chinese lunar calendar
            'r' => '',        // related Gregorian year e.g. 1996
            'Q' => '',      // number of quarter
            'QQ' => '',     // number of quarter '02'
            'QQQ' => '',    // quarter 'Q2'
            'QQQQ' => '',   // quarter '2nd quarter'
            'QQQQQ' => '',  // number of quarter '2'
            'q' => '',      // number of Stand Alone quarter
            'qq' => '',     // number of Stand Alone quarter '02'
            'qqq' => '',    // Stand Alone quarter 'Q2'
            'qqqq' => '',   // Stand Alone quarter '2nd quarter'
            'qqqqq' => '',  // number of Stand Alone quarter '2'
            'M' => 'n',     // Numeric representation of a month, without leading zeros
            'MM' => 'm',    // Numeric representation of a month, with leading zeros
            'MMM' => 'M',   // A short textual representation of a month, three letters
            'MMMM' => 'F',  // A full textual representation of a month, such as January or March
            'MMMMM' => '',  //
            'L' => 'n',     // Stand alone month in year
            'LL' => 'm',    // Stand alone month in year
            'LLL' => 'M',   // Stand alone month in year
            'LLLL' => 'F',  // Stand alone month in year
            'LLLLL' => '',  // Stand alone month in year
            'w' => 'W',     // ISO-8601 week number of year
            'ww' => 'W',    // ISO-8601 week number of year
            'W' => '',      // week of the current month
            'd' => 'j',     // day without leading zeros
            'dd' => 'd',    // day with leading zeros
            'D' => 'z',     // day of the year 0 to 365
            'F' => '',      // Day of Week in Month. eg. 2nd Wednesday in July
            'g' => '',      // Modified Julian day. This is different from the conventional Julian day number in two regards.
            'E' => 'D',     // day of week written in short form eg. Sun
            'EE' => 'D',
            'EEE' => 'D',
            'EEEE' => 'l',  // day of week fully written eg. Sunday
            'EEEEE' => '',
            'EEEEEE' => '',
            'e' => 'N',     // ISO-8601 numeric representation of the day of the week 1=Mon to 7=Sun
            'ee' => 'N',    // php 'w' 0=Sun to 6=Sat isn't supported by ICU -> 'w' means week number of year
            'eee' => 'D',
            'eeee' => 'l',
            'eeeee' => '',
            'eeeeee' => '',
            'c' => 'N',     // ISO-8601 numeric representation of the day of the week 1=Mon to 7=Sun
            'cc' => 'N',    // php 'w' 0=Sun to 6=Sat isn't supported by ICU -> 'w' means week number of year
            'ccc' => 'D',
            'cccc' => 'l',
            'ccccc' => '',
            'cccccc' => '',
            'a' => 'a',     // am/pm marker
            'h' => 'g',     // 12-hour format of an hour without leading zeros 1 to 12h
            'hh' => 'h',    // 12-hour format of an hour with leading zeros, 01 to 12 h
            'H' => 'G',     // 24-hour format of an hour without leading zeros 0 to 23h
            'HH' => 'H',    // 24-hour format of an hour with leading zeros, 00 to 23 h
            'k' => '',      // hour in day (1~24)
            'kk' => '',     // hour in day (1~24)
            'K' => '',      // hour in am/pm (0~11)
            'KK' => '',     // hour in am/pm (0~11)
            'm' => 'i',     // Minutes without leading zeros, not supported by php but we fallback
            'mm' => 'i',    // Minutes with leading zeros
            's' => 's',     // Seconds, without leading zeros, not supported by php but we fallback
            'ss' => 's',    // Seconds, with leading zeros
            'S' => '',      // fractional second
            'SS' => '',     // fractional second
            'SSS' => '',    // fractional second
            'SSSS' => '',   // fractional second
            'A' => '',      // milliseconds in day
            'z' => 'T',     // Timezone abbreviation
            'zz' => 'T',    // Timezone abbreviation
            'zzz' => 'T',   // Timezone abbreviation
            'zzzz' => 'T',  // Timzone full name, not supported by php but we fallback
            'Z' => 'O',     // Difference to Greenwich time (GMT) in hours
            'ZZ' => 'O',    // Difference to Greenwich time (GMT) in hours
            'ZZZ' => 'O',   // Difference to Greenwich time (GMT) in hours
            'ZZZZ' => '\G\M\TP', // Time Zone: long localized GMT (=OOOO) e.g. GMT-08:00
            'ZZZZZ' => '',  //  TIme Zone: ISO8601 extended hms? (=XXXXX)
            'O' => '',      // Time Zone: short localized GMT e.g. GMT-8
            'OOOO' => '\G\M\TP', //  Time Zone: long localized GMT (=ZZZZ) e.g. GMT-08:00
            'v' => '\G\M\TP', // Time Zone: generic non-location (falls back first to VVVV and then to OOOO) using the ICU defined fallback here
            'vvvv' => '\G\M\TP', // Time Zone: generic non-location (falls back first to VVVV and then to OOOO) using the ICU defined fallback here
            'V' => '',      // Time Zone: short time zone ID
            'VV' => 'e',    // Time Zone: long time zone ID
            'VVV' => '',    // Time Zone: time zone exemplar city
            'VVVV' => '\G\M\TP', // Time Zone: generic location (falls back to OOOO) using the ICU defined fallback here
            'X' => '',      // Time Zone: ISO8601 basic hm?, with Z for 0, e.g. -08, +0530, Z
            'XX' => 'O, \Z', // Time Zone: ISO8601 basic hm, with Z, e.g. -0800, Z
            'XXX' => 'P, \Z',    // Time Zone: ISO8601 extended hm, with Z, e.g. -08:00, Z
            'XXXX' => '',   // Time Zone: ISO8601 basic hms?, with Z, e.g. -0800, -075258, Z
            'XXXXX' => '',  // Time Zone: ISO8601 extended hms?, with Z, e.g. -08:00, -07:52:58, Z
            'x' => '',      // Time Zone: ISO8601 basic hm?, without Z for 0, e.g. -08, +0530
            'xx' => 'O',     // Time Zone: ISO8601 basic hm, without Z, e.g. -0800
            'xxx' => 'P',    // Time Zone: ISO8601 extended hm, without Z, e.g. -08:00
            'xxxx' => '',   // Time Zone: ISO8601 basic hms?, without Z, e.g. -0800, -075258
            'xxxxx' => '',  // Time Zone: ISO8601 extended hms?, without Z, e.g. -08:00, -07:52:58
        ]));
    }
    
    /**
     * Return format pattern settings of current locale.
     * @return array
     * @throws InvalidConfigException
     */
    protected function getPattern()
    {
        $locale = Yii::$app->language;
        if (!isset($this->patterns[$locale])) {
            throw new InvalidConfigException("Locale key '{$locale}' not found in `\$patterns`.");
        }
        return $this->patterns[$locale];
    }
}
