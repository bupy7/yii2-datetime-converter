<?php

namespace tests\models;

use Yii;
use yii\base\Model;
use bupy7\datetime\converter\Converter;

/**
 * Test model of `bupy7\datetime\converter\Converter` behavior.
 * @author Belosludcev Vasilij <https://github.com/bupy7>
 * @since 1.1.1
 */
class Post extends Model
{    
    /**
     * @var string
     */
    public $time;
    /**
     * @var string
     */
    public $date;
    /**
     * @var string
     */
    public $datetime;
    /**
     * @var array
     */
    public $preparedData = [];
    /**
     * @var Converter
     */
    protected $dtConverter;
    
    /**
     * @inheritdoc
     * @param Converter $dtConverter
     */
    public function __construct(Converter $dtConverter, $config = [])
    {
        parent::__construct($config);
        $this->dtConverter = $dtConverter;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['time'], 'date', 'format' => $this->dtConverter->saveTime],
            [['date'], 'date', 'format' => $this->dtConverter->saveDate],
            [['datetime'], 'date', 'format' => $this->dtConverter->saveDateTime],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }
        foreach ($this->activeAttributes() as $attribute) {
            $this->preparedData[$attribute] = $this->$attribute;
        }
        return true;
    }
    
    /**
     * @return array
     */
    public function getPreparedData()
    {
        return $this->preparedData;
    }
}