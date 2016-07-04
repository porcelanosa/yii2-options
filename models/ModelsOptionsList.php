<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "models_options_list".
 *
 * @property integer $model_id
 * @property string $model_name
 * @property integer $option_id
 * @property integer $sort
 */
class ModelsOptionsList extends \yii\db\ActiveRecord
{
    public static function primaryKey()
    {
        return [
            'model_id',
            'option_id',
            'model_name'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'models_options_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model_id', 'option_id', 'sort'], 'integer'],
            [['model_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'model_id' => Yii::t('app', 'Model ID'),
            'model_name' => Yii::t('app', 'Model Name'),
            'option_id' => Yii::t('app', 'Option ID'),
            'sort' => Yii::t('app', 'Sort'),
        ];
    }

    /**
     * @inheritdoc
     * @return \app\modules\admin\models\query\ModelsOptionsListQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\modules\admin\models\query\ModelsOptionsListQuery(get_called_class());
    }
}
