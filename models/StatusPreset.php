<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "status_preset".
 *
 * @property integer $id
 * @property string $model_name
 * @property string $name
 * @property integer $active
 * @property integer $sort
 *
 * @property CatStatuses[] $catStatuses
 * @property ItemStatuses[] $itemStatuses
 * @property StatusPresetValues[] $statusPresetValues
 */
class StatusPreset extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'status_preset';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['active', 'sort'], 'integer'],
            [['model_name', 'name'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'model_name' => Yii::t('app', 'Model Name'),
            'name' => Yii::t('app', 'Name'),
            'active' => Yii::t('app', 'Active'),
            'sort' => Yii::t('app', 'Sort'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatStatuses()
    {
        return $this->hasMany(CatStatuses::className(), ['preset_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemStatuses()
    {
        return $this->hasMany(ItemStatuses::className(), ['preset_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatusPresetValues()
    {
        return $this->hasMany(StatusPresetValues::className(), ['preset_id' => 'id'])->where(['active'=>1])->orderBy(['sort' => SORT_ASC]);
    }

    /**
     * @inheritdoc
     * @return \app\modules\admin\models\query\StatusPresetQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\modules\admin\models\query\StatusPresetQuery(get_called_class());
    }
}
