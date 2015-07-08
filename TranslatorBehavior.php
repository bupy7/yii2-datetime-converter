<?php

namespace bupy7\date\translator;

use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\base\Event;
use yii\di\Instance;

/**
 * 
 * @author Belosludcev Vasilij <bupy765@gmail.com>
 */
class TranslatorBehavior extends Behavior
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
     * @var Translator|string|array
     */
    public $translator = 'dateTranslator';
    /**
     * @var integer Type of attribute for translation.
     */
    public $type = self::TYPE_DATE_TIME;
    /**
     * @var integer Pointer translate to.
     */
    public $to = self::TO_SAVE;
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
        if (is_string($this->translator)) {
            $this->translator = Instance::ensure($this->translator, Translator::className());
        } elseif (is_array($this->translator)) {
            $this->translator = Yii::createObject($this->translator);
        }
        if (!($this->translator instanceof Translator)) {
            throw new InvalidConfigException('Invalid configuration of $translator property.');
        }
        if (!in_array($this->type, [self::TYPE_DATE, self::TYPE_TIME, self::TYPE_DATE_TIME])) {
            throw new InvalidConfigException('Invalid configuration of $type property.');
        }
        if (!in_array($this->to, [self::TO_SAVE, self::TO_DISPLAY])) {
            throw new InvalidConfigException('Invalid configuration of $to property.');
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
        return array_fill_keys(array_keys($this->attributes), 'evaluateAttributes');
    }
    
    /**
     * Evaluates the attribute date translator and assigns it to the current attributes.
     * @param Event $event
     */
    public function evaluateAttributes($event)
    {
        if (!empty($this->attributes[$event->name])) {
            $attributes = (array) $this->attributes[$event->name];
            foreach ($attributes as $attribute) {
                // ignore attribute names which are not string
                if (is_string($attribute)) {
                    $this->owner->$attribute = $this->translator->{$this->_method}($this->owner->$attribute);
                }
            }
        }
    }
}