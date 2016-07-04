<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "child_option_multiple".
 *
 * @property integer $id
 * @property integer $option_id
 * @property string $value
 */
class ChildOptionMultiple extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'child_option_multiple';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['option_id'], 'integer'],
            [['value'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'option_id' => Yii::t('app', 'Option ID'),
            'value' => Yii::t('app', 'Value'),
        ];
    }

    /**
     * @inheritdoc
     * @return \app\modules\admin\models\query\ChildOptionMultipleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\modules\admin\models\query\ChildOptionMultipleQuery(get_called_class());
    }
}
