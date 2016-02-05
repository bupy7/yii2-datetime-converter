<?php

namespace tests\models;

use Yii;
use yii\base\Model;
use bupy7\datetime\converter\Converter;

/**
 * Example the post test model.
 * @author Belosludcev Vasilij <https://github.com/bupy7>
 * @since 1.1.1
 */
class Post extends Model
{    
    /**
     * @var string
     */
    public $created_at;
    /**
     * @var Converter
     */
    protected $dtConverter;
    
    /**
     * @inheritdoc
     * @param Converter $dtConverter
     */
    public function init(Converter $dtConverter)
    {
        parent::init();
        $this->dtConverter = $dtConverter;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at'], 'date', 'format' => $this->dtConverter->displayDateTime],
        ];
    }
}