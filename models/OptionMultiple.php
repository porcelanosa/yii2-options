<?php
	
	namespace porcelanosa\yii2options\models;
	
	use Yii;
	
	/**
	 * This is the model class for table "option_multiple".
	 *
	 * @property integer $id
	 * @property integer $option_id
	 * @property string  $value
	 */
	class OptionMultiple extends \yii\db\ActiveRecord {
		/**
		 * @inheritdoc
		 */
		public static function tableName() {
			return 'option_multiple';
		}
		
		/**
		 * @inheritdoc
		 */
		public function rules() {
			return [
				[ [ 'id', 'option_id' ], 'integer' ],
				[ [ 'value' ], 'string', 'max' => 255 ],
			];
		}
		
		/**
		 * @inheritdoc
		 */
		public function attributeLabels() {
			return [
				'id'        => Yii::t( 'app', 'ID' ),
				'option_id' => Yii::t( 'app', 'Option ID' ),
				'value'     => Yii::t( 'app', 'Value' ),
			];
		}
		
		/**
		 * @inheritdoc
		 * @return \porcelanosa\yii2options\models\query\OptionMultipleQuery the active query used by this AR class.
		 */
		public static function find() {
			return new \porcelanosa\yii2options\models\query\OptionMultipleQuery( get_called_class() );
		}
	}
