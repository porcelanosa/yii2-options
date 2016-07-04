<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "rich_texts".
 *
 * @property integer $id
 * @property integer $option_id
 * @property string $title
 * @property string $text
 */
class RichTexts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rich_texts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['option_id'], 'integer'],
            [['text'], 'string'],
            [['title'], 'string', 'max' => 255],
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
            'title' => Yii::t('app', 'Title'),
            'text' => Yii::t('app', 'Text'),
        ];
    }

    /**
     * @inheritdoc
     * @return \app\modules\admin\models\query\RichTextsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\modules\admin\models\query\RichTextsQuery(get_called_class());
    }
}
