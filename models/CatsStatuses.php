<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "cats_statuses".
 *
 * @property integer $cat_id
 * @property integer $status_id
 * @property string $value
 * @property string $alias
 *
 * @property Cats $cat
 * @property CatStatuses $status
 */
class CatsStatuses extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cats_statuses';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_id', 'status_id'], 'integer'],
            [['value'], 'string', 'max' => 255],
            [['cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cats::className(), 'targetAttribute' => ['cat_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => CatStatuses::className(), 'targetAttribute' => ['status_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cat_id' => Yii::t('app', 'Cat ID'),
            'status_id' => Yii::t('app', 'Status ID'),
            'value' => Yii::t('app', 'Value')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCat()
    {
        return $this->hasOne(Cats::className(), ['id' => 'cat_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(CatStatuses::className(), ['id' => 'status_id']);
    }
}
