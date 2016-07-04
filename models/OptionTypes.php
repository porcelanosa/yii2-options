<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "option_types".
 *
 * @property integer $id
 * @property string $name
 * @property string $alias
 * @property integer $sort
 * @property string $active
 */
class OptionTypes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'option_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['alias', 'active'], 'string', 'max' => 255],
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
            'sort' => Yii::t('app', 'Sort'),
            'active' => Yii::t('app', 'Active'),
        ];
    }

    /**
     * @inheritdoc
     * @return \app\modules\admin\models\query\OptionTypesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\modules\admin\models\query\OptionTypesQuery(get_called_class());
    }
}
