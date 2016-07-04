<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "options".
 *
 * @property integer $id
 * @property string $model
 * @property integer $model_id
 * @property string $value
 * @property integer $option_id
 */
class Options extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'options';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model_id', 'option_id'], 'integer'],
            [['model', 'value'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'model' => Yii::t('app', 'Model'),
            'model_id' => Yii::t('app', 'Model ID'),
            'value' => Yii::t('app', 'Value'),
            'option_id' => Yii::t('app', 'Option ID'),
        ];
    }

    /**
     * @inheritdoc
     * @return \app\modules\admin\models\query\OptionsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\modules\admin\models\query\OptionsQuery(get_called_class());
    }
}
