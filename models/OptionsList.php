<?php
	
	namespace porcelanosa\yii2options\models;
	
	use Yii;
	
	/**
	 * This is the model class for table "options_list".
	 *
	 * @property integer       $id
	 * @property string        $name
	 * @property string        $alias
	 * @property string        $model
	 * @property integer       $is_required
	 * @property integer       $in_filter
	 * @property integer       $type_id
	 * @property integer       $preset_id
	 * @property integer       $parent_id
	 * @property integer       $active
	 * @property integer       $sort
	 * @property integer       $minLenght
	 * @property integer       $maxLenght
	 * @property OptionTypes   $type
	 * @property OptionPresets $preset
	 */
	class OptionsList extends \yii\db\ActiveRecord {
		/**
		 * @inheritdoc
		 */
		public static function tableName() {
			return 'options_list';
		}
		
		/**
		 * @inheritdoc
		 */
		public function rules() {
			return [
				[
					[
						'is_required',
						'in_filter',
						'type_id',
						'preset_id',
						'parent_id',
						'active',
						'sort',
						'minLenght',
						'maxLenght'
					],
					'integer'
				],
				[ [ 'name' ], 'string', 'max' => 50 ],
				[ [ 'model' ], 'string', 'max' => 250 ],
				//[['model'], 'required'],
				[ [ 'alias', 'model' ], 'string', 'max' => 255 ],
			];
		}
		
		/**
		 * @inheritdoc
		 */
		public function attributeLabels() {
			return [
				'id'          => Yii::t( 'app', 'ID' ),
				'name'        => Yii::t( 'app', 'Name' ),
				'alias'       => Yii::t( 'app', 'Alias' ),
				'model'       => Yii::t( 'app', 'Model' ),
				'is_required' => Yii::t( 'app', 'Is Required' ),
				'in_filter'   => Yii::t( 'app', 'ADMIN_LIST_IN_FILTER' ),
				'type_id'     => Yii::t( 'app', 'Type ID' ),
				'preset_id'   => Yii::t( 'app', 'ADMIN_PRESET' ),
				'parent_id'   => Yii::t( 'app', 'ADMIN_PARENT_ID' ),
				'active'      => Yii::t( 'app', 'Active' ),
				'sort'        => Yii::t( 'app', 'Sort' ),
				'minLenght'   => Yii::t( 'app', 'Min Lenght' ),
				'maxLenght'   => Yii::t( 'app', 'Max Lenght' ),
			];
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getType() {
			return $this->hasOne( OptionTypes::className(), [ 'id' => 'type_id' ] );
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getPreset() {
			return $this->hasOne( OptionPresets::className(), [ 'id' => 'preset_id' ] );
		}
		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getOptions() {
			return $this->hasMany( Options::className(), [ 'option_id' => 'id' ] );
		}
		
		
		/**
		 * @inheritdoc
		 * @return \porcelanosa\yii2options\models\query\OptionsListQuery the active query used by this AR class.
		 */
		public static function find() {
			return new \porcelanosa\yii2options\models\query\OptionsListQuery( get_called_class() );
		}
	}
