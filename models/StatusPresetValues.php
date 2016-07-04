<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "status_preset_values".
 *
 * @property integer $id
 * @property integer $preset_id
 * @property string $value
 * @property integer $sort
 *
 * @property StatusPreset $preset
 */
class StatusPresetValues extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'status_preset_values';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['preset_id', 'sort'], 'integer'],
            [['value'], 'string', 'max' => 245],
            [['preset_id'], 'exist', 'skipOnError' => true, 'targetClass' => StatusPreset::className(), 'targetAttribute' => ['preset_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'preset_id' => Yii::t('app', 'Preset ID'),
            'value' => Yii::t('app', 'Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreset()
    {
        return $this->hasOne(StatusPreset::className(), ['id' => 'preset_id']);
    }

    /**
     * @inheritdoc
     * @return \app\modules\admin\models\query\StatusPresetValuesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\modules\admin\models\query\StatusPresetValuesQuery(get_called_class());
    }
}
