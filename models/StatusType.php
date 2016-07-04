<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "status_type".
 *
 * @property integer $id
 * @property integer $has_preset
 * @property string $name
 * @property string $alias
 */
class StatusType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'status_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'has_preset'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['alias'], 'string', 'max' => 55],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'has_preset' => Yii::t('app', 'Наличие набора данных'),
            'name' => Yii::t('app', 'Name'),
            'alias' => Yii::t('app', 'Alias'),
        ];
    }

    /**
     * @inheritdoc
     * @return \app\modules\admin\models\query\StatusTypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\modules\admin\models\query\StatusTypeQuery(get_called_class());
    }
}
