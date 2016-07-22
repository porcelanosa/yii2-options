<?php
    
    namespace porcelanosa\yii2options\models;
    
    use Yii;
    
    /**
     * This is the model class for table "porcelanosa_options_child_rich_texts".
     *
     * @property integer $id
     * @property integer $option_id
     * @property string  $title
     * @property string  $text
     */
    class ChildRichTexts extends \yii\db\ActiveRecord
    {
        /**
         * @inheritdoc
         */
        public static function tableName()
        {
            return 'porcelanosa_options_child_rich_texts';
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
                'id'        => Yii::t('backend', 'ID'),
                'option_id' => Yii::t('backend', 'Option ID'),
                'title'     => Yii::t('backend', 'Title'),
                'text'      => Yii::t('backend', 'Text'),
            ];
        }
        
        /**
         * @inheritdoc
         * @return \porcelanosa\yii2options\models\query\ChildRichTextsQuery the active query used by this AR
         *                                                                          class.
         */
        public static function find()
        {
            return new \porcelanosa\yii2options\models\query\ChildRichTextsQuery(get_called_class());
        }
    }
