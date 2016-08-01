<?php
	
	namespace porcelanosa\yii2options\models;
	
	use porcelanosa\yii2options\models\query\RichTextsQuery;
	use Yii;
	
	/**
	 * This is the model class for table "rich_texts".
	 *
	 * @property integer $id
	 * @property integer $option_id
	 * @property string  $title
	 * @property string  $text
	 */
	class RichTexts extends \yii\db\ActiveRecord {
		/**
		 * @inheritdoc
		 */
		public static function tableName() {
			return 'rich_texts';
		}
		
		/**
		 * @inheritdoc
		 */
		public function rules() {
			return [
				[ [ 'option_id' ], 'integer' ],
				[ [ 'text' ], 'text' ],
				[ [ 'title' ], 'string', 'max' => 255 ],
			];
		}
		
		/**
		 * @inheritdoc
		 */
		public function attributeLabels() {
			return [
				'id'        => Yii::t( 'app', 'ID' ),
				'option_id' => Yii::t( 'app', 'Option ID' ),
				'title'     => Yii::t( 'app', 'Title' ),
				'text'      => Yii::t( 'app', 'Text' ),
			];
		}
		
		/**
		 * @inheritdoc
		 * @return RichTextsQuery the active query used by this AR class.
		 */
		public static function find() {
			return new RichTextsQuery( get_called_class() );
		}
	}
