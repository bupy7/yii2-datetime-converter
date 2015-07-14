<?php

namespace bupy7\datetime\converter;

use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\base\Event;
use yii\di\Instance;

/**
 * Converter date/time behavior for models.
 * 
 * Usage:
 * 
 * ~~~
 *  public function behaviors()
 *  {
 *       return [
 *          // converter date/time before save
 *          [
 *              'class' => ConverterBehavior::className(),
 *              'type' => ConverterBehavior::TYPE_DATE_TIME,
 *              'to' => ConverterBehavior::TO_SAVE,
 *              'attributes' => [
 *                  self::EVENT_BEFORE_SAVE => ['attribute_1', 'attribute_2'],
 *              ],
 *          ],
 *      ];
 *  }
 * ~~~
 * 
 * Result (example):
 * 
 * ~~~
 * 01.01.2015 23:54:00 => 1420156440 
 * ~~~
 * 
 * @author Belosludcev Vasilij <https://github.com/bupy7>
 * @since 1.0.0
 */
class ConverterBehavior extends Behavior
{
    /**
     * Type of attribute: date.
     */
    const TYPE_DATE = 1;
    /**
     * Type of attribute: time.
     */
    const TYPE_TIME = 2;
    /**
     * Type of attribute: date and time.
     */
    const TYPE_DATE_TIME = 3;
    
    /**
     * Translate to saving.
     */
    const TO_SAVE = 1;
    /**
     * Translate to display.
     */
    const TO_DISPLAY = 2;
    
    /**
     * @var Converter|string|array Converter of date/time. This may be name of component in application, 
     * array configuration or instance of Converter class. By deault it name of component.
     */
    public $converter = 'dtConverter';
    /**
     * @var integer Type of attribute for converter.
     */
    public $type;
    /**
     * @var integer Converter to save/display.
     */
    public $to;
    /**
     * @var array list of attributes that are to be automatically filled with the value specified via [[value]].
     * The array keys are the ActiveRecord events upon which the attributes are to be updated,
     * and the array values are the corresponding attribute(s) to be updated. You can use a string to represent
     * a single attribute, or an array to represent a list of attributes. For example,
     *
     * ```php
     * [
     *     ActiveRecord::EVENT_BEFORE_INSERT => ['attribute1', 'attribute2'],
     *     ActiveRecord::EVENT_BEFORE_UPDATE => 'attribute2',
     * ]
     * ```
     */
    public $attributes = [];
    
    private $_method;
    
    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (is_string($this->converter)) {
            $this->converter = Instance::ensure($this->converter, Converter::className());
        } elseif (is_array($this->converter)) {
            $this->converter = Yii::createObject($this->converter);
        }
        if (!($this->converter instanceof Converter)) {
            throw new InvalidConfigException('Invalid configuration of `$converter` property.');
        }
        if (empty($this->type) || !in_array($this->type, [self::TYPE_DATE, self::TYPE_TIME, self::TYPE_DATE_TIME])) {
            throw new InvalidConfigException('Invalid configuration of `$type` property.');
        }
        if (empty($this->to) || !in_array($this->to, [self::TO_SAVE, self::TO_DISPLAY])) {
            throw new InvalidConfigException('Invalid configuration of `$to` property.');
        }
        $this->_method = 'to';
        switch ($this->to) {
            case self::TO_SAVE:
                $this->_method .= 'Save';
                break;
            case self::TO_DISPLAY:
                $this->_method .= 'Display';
        }
        switch ($this->type) {
            case self::TYPE_TIME:
                $this->_method .= 'Time';
                break;
            case self::TYPE_DATE:
                $this->_method .= 'Date';
                break;
            case self::TYPE_DATE_TIME:
                $this->_method .= 'DateTime';
                break;
        }
    }
    
    /**
     * @inheritdoc
     */
    public function events()
    {
        return array_fill_keys(array_keys($this->attributes), 'convertingAttributes');
    }
    
    /**
     * Converting value attribute and assigns it to the current attributes.
     * @param Event $event
     */
    public function convertingAttributes($event)
    {
        if (!empty($this->attributes[$event->name])) {
            $attributes = (array) $this->attributes[$event->name];
            foreach ($attributes as $attribute) {
                if (!empty($this->owner->$attribute)) {
                    $this->owner->$attribute = $this->converter->{$this->_method}($this->owner->$attribute);
                }
            }
        }
    }
}