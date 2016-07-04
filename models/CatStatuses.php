<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "cat_statuses".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type_id
 * @property integer $sort
 * @property integer $preset_id
 *
 * @property StatusType $type
 * @property StatusPreset $preset
 * @property CatsStatuses[] $catsStatuses
 */
class CatStatuses extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cat_statuses';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_id', 'sort', 'preset_id'], 'integer'],
            [['name', 'alias'], 'string', 'max' => 50],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => StatusType::className(), 'targetAttribute' => ['type_id' => 'id']],
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
            'name' => Yii::t('app', 'Name'),
            'alias' => Yii::t('app', 'Alias'),
            'type_id' => Yii::t('app', 'Type ID'),
            'sort' => Yii::t('app', 'Sort'),
            'preset_id' => Yii::t('app', 'Preset ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(StatusType::className(), ['id' => 'type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreset()
    {
        return $this->hasOne(StatusPreset::className(), ['id' => 'preset_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatsStatuses()
    {
        return $this->hasMany(CatsStatuses::className(), ['status_id' => 'id']);
    }
}
