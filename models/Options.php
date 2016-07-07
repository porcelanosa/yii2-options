<?php
	
	namespace porcelanosa\yii2options\models;
	
	use Yii;
	
	/**
	 * This is the model class for table "options".
	 *
	 * @property integer $id
	 * @property string  $model
	 * @property integer $model_id
	 * @property string  $value
	 * @property integer $option_id
	 */
	class Options extends \yii\db\ActiveRecord {
		/**
		 * @inheritdoc
		 */
		public static function tableName() {
			return 'options';
		}
		
		/**
		 * @inheritdoc
		 */
		public function rules() {
			return [
				[ [ 'model_id', 'option_id' ], 'integer' ],
				[ [ 'model', 'value' ], 'string', 'max' => 255 ],
			];
		}
		
		/**
		 * @inheritdoc
		 */
		public function attributeLabels() {
			return [
				'id'        => Yii::t( 'app', 'ID' ),
				'model'     => Yii::t( 'app', 'Model' ),
				'model_id'  => Yii::t( 'app', 'Model ID' ),
				'value'     => Yii::t( 'app', 'Value' ),
				'option_id' => Yii::t( 'app', 'Option ID' ),
			];
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 * Возвращает список значений если есть
		 */
		public function getMultipleOptions() {
			return $this->hasMany( OptionMultiple::className(), [ 'option_id' => 'id' ] );
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getOptionsList() {
			return $this->hasOne( Options::className(), [ 'id' => 'option_id' ] );
		}
		
		/**
		 * @inheritdoc
		 * @return \porcelanosa\yii2options\models\query\OptionsQuery the active query used by this AR class.
		 */
		public static function find() {
			return new \porcelanosa\yii2options\models\query\OptionsQuery( get_called_class() );
		}
	}
