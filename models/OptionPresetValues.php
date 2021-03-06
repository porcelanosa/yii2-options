<?php
	
	namespace porcelanosa\yii2options\models;
	
	use Yii;
	use \porcelanosa\yii2options\models\query\OptionPresetValuesQuery;
	/**
	 * This is the model class for table "option_preset_values".
	 *
	 * @property integer $id
	 * @property integer $preset_id
	 * @property string  $value
	 * @property integer $sort
	 */
	class OptionPresetValues extends \yii\db\ActiveRecord {
		/**
		 * @inheritdoc
		 */
		public static function tableName() {
			return 'option_preset_values';
		}
		
		/**
		 * @inheritdoc
		 */
		public function rules() {
			return [
				[ [ 'preset_id', 'sort' ], 'integer' ],
				[ [ 'value' ], 'string', 'max' => 255 ],
			];
		}
		
		/**
		 * @inheritdoc
		 */
		public function attributeLabels() {
			return [
				'id'        => Yii::t( 'app', 'ID' ),
				'preset_id' => Yii::t( 'app', 'Preset ID' ),
				'value'     => Yii::t( 'app', 'Value' ),
				'sort'      => Yii::t( 'app', 'Sort' ),
			];
		}
		
		/**
		 * @inheritdoc
		 * @return OptionPresetValuesQuery the active query used by this AR class.
		 */
		public static function find() {
			return new OptionPresetValuesQuery( get_called_class() );
		}
	}
