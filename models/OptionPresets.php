<?php
	
	namespace porcelanosa\yii2options\models;
	
	
	use Yii;
	
	/**
	 * This is the model class for table "option_presets".
	 *
	 * @property integer $id
	 * @property string  $name
	 * @property string  $short_name
	 * @property integer $defaultPresetValue_id
	 * @property integer $sort
	 * @property integer $active
	 * @property OptionPresetValues $optionPresetValues
	 */
	class OptionPresets extends \yii\db\ActiveRecord {
		/**
		 * @inheritdoc
		 */
		public static function tableName() {
			return 'option_presets';
		}
		
		/**
		 * @inheritdoc
		 */
		public function rules() {
			return [
				[ [ 'defaultPresetValue_id', 'sort', 'active' ], 'integer' ],
				[ [ 'name', 'short_name' ], 'string', 'max' => 50 ],
			];
		}
		
		/**
		 * @inheritdoc
		 */
		public function attributeLabels() {
			return [
				'id'                    => Yii::t( 'app', 'ID' ),
				'name'                  => Yii::t( 'app', 'Name' ),
				'short_name'            => Yii::t( 'app', 'Short Name' ),
				'defaultPresetValue_id' => Yii::t( 'app', 'Default Preset Value ID' ),
				'sort'                  => Yii::t( 'app', 'Sort' ),
				'active'                => Yii::t( 'app', 'Active' ),
			];
		}
		
		/**
		 * @inheritdoc
		 * @return \porcelanosa\yii2options\models\query\OptionPresetsQuery the active query used by this AR class.
		 */
		public static function find() {
			return new \porcelanosa\yii2options\models\query\OptionPresetsQuery( get_called_class() );
		}
		
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getOptionPresetValues() {
			return $this->hasMany( OptionPresetValues::className(), [ 'preset_id' => 'id' ] )->where( [ 'active' => 1 ] )->orderBy( [ 'sort' => SORT_ASC ] );
		}
	}
