<?php
	namespace porcelanosa\yii2options;
	
	use app\components\helpers\MyHelper;
	use app\modules\admin\models\Cats;
	use porcelanosa\yii2options\models\ChildOptionMultiple;
	use porcelanosa\yii2options\models\ChildOptions;
	use porcelanosa\yii2options\models\ModelsOptionsList;
	use porcelanosa\yii2options\models\OptionMultiple;
	use porcelanosa\yii2options\models\OptionPresetValues;
	use porcelanosa\yii2options\models\Options;
	use porcelanosa\yii2options\models\OptionsList;
	use Yii;
	use yii\behaviors\AttributeBehavior;
	
	use yii\db\ActiveRecord;
	use yii\helpers\ArrayHelper;
	use yii\web\Controller;
	use yii\helpers\Html;
	
	
	use yii\web\View;
	
	/*
	 *
	 * */
	
	class ChildOptionsBehavior
		extends AttributeBehavior {
		
		public $model_name = 'Items';
		public $options_string = '';
		public $parent_model_name = 'Cats';
		public $parent_model_full_name = MyHelper::ADMIN_MODEL_NAMESPACE . 'Cats';
		public $parent_relation = 'cat';
		
		public function events() {
			return [
				ActiveRecord::EVENT_BEFORE_UPDATE => 'saveOptions',
			];
		}
		
		
		public function saveOptions() {
			
			$model  = $this->owner;
			$cat_id = $model->{$this->parent_relation}->id;
			
			//  обрабатываем поля статусов
			foreach($this->getChildOptionsList($cat_id) as $option) {
				$option_name     = trim(str_replace(' ', '_', $option->alias));
				$option_type     = $option->type->alias;
				$postOptinonName = $_POST[ $option_name ] ? $_POST[ $option_name ] : 0;
				// Есть ли такой статус ???
				$is_exist_status = $this->ifOptionExist($option->id);
				
				if(!$is_exist_status) { // ДОБАВЛЯЕМ если нет
					Yii::$app->db->createCommand()
					             ->insert('child_options',
						             [
							             'value'     => $postOptinonName,
							             'model'     => $this->parent_model_name . '-' . $this->model_name,
							             'model_id'  => $model->id,
							             'option_id' => $option->id,
						             ]
					             )->execute()
					;
					$last_insert_option_id = Yii::$app->db->getLastInsertID();
					$this->setMultipleOptions($option_type, $option_name, $last_insert_option_id);
					
				} else {
					//var_dump($postOptinonName);
					// ОБНОВЛЯЕМ если есть
					Yii::$app->db->createCommand()
					             ->update('child_options',
						             ['value' => $postOptinonName],
						             [
							             'model'     => $this->parent_model_name . '-' . $this->model_name,
							             'model_id'  => $model->id,
							             'option_id' => $option->id,
						             ]
					             )->execute()
					;
					$opt = ChildOptions::findOne([
						'model_id'  => $model->id,
						'option_id' => $option->id,
					]);
					$this->setMultipleOptions($option_type, $option_name, $opt->id);
				}
			}
		}

		public function setMultipleOptions($option_type, $option_name, $option_id) {
			if(in_array($option_type, MyHelper::TYPES_WITH_MULTIPLE_PRESET_ARRAY)) {
				$option_array = Yii::$app->request->post($option_name);
				if(is_array($option_array)) {
					// удаляем все значения с этим option_id
					$curent_options = ChildOptionMultiple::find()->where(['option_id' => $option_id])->all();
					foreach($curent_options as $c_opt) {
						$c_opt->delete();
					}
					foreach($option_array as $option_value) {
						$newOptionMultiple            = new ChildOptionMultiple();
						$newOptionMultiple->option_id = $option_id;
						$newOptionMultiple->value     = $option_value;
						$newOptionMultiple->save();
					}
				}
			}
		}

		/**
		 * Определяем есть ли у работы такой статус и если есть, то возвращаем его
		 *
		 * @param $option_id integer
		 *
		 * @return mixed
		 */
		public function getChildOptionValueById($option_id) {
			
			$option = (new \yii\db\Query())
				->select('value')
				->from('child_options')
				->where(
					[
						'model_id'  => $this->owner->id,
						'model'     => $this->parent_model_name . '-' . $this->model_name,
						'option_id' => $option_id
					]
				)->one()
			;
			//var_dump($cats_statuses['value']);
			
			/*if (is_null($works_statuses['value'])) {
				return false;
			} else {
			}*/
			
			return $option['value'];
			
		}
		
		/**
		 * Определяем есть ли у работы такой статус и если есть, то возвращаем его
		 *
		 * @param $option_id integer
		 *
		 * @return mixed
		 */
		public function getChildOptionValueByOptionId($option_id) {
			
			$option = (new \yii\db\Query())
				->select('value')
				->from('childe_options')
				->where(
					[
						'option_id' => $option_id
					]
				)->one()
			;
			//var_dump($cats_statuses['value']);
			
			/*if (is_null($works_statuses['value'])) {
				return false;
			} else {
			}*/
			
			return $option['value'];
			
		}
		
		/**
		 * Определяем есть ли у работы такой статус и если есть, то возвращаем его
		 *
		 * @param $option_id integer
		 *
		 * @return mixed
		 */
		public function getChildOptionMultipleValueByOptionId($option_id) {
			$return_array = [];
			$options      = ChildOptionMultiple::find()
			                                   ->select('value')
			                                   ->where(
				                                   [
					                                   'option_id' => $option_id
				                                   ]
			                                   )->asArray()->all()
			;
			foreach($options as $option) {
				$return_array[] = $option['value'];
			}
			
			return $return_array;
			
		}
		
		/**
		 * Определяем есть ли у работы этот статус ВООБЩЕ
		 *
		 * @param $status_name
		 *
		 * @return boolean
		 */
		public function ifOptionExist($option_id) {
			
			$option = (new \yii\db\Query())
				->select(['value'])
				->from('child_options')
				->where(
					[
						'model'     => $this->parent_model_name . '-' . $this->model_name,
						'model_id'  => $this->owner->id,
						'option_id' => $option_id
					]
				)
				->one()
			;
			
			if(count($option['value']) == 0) {
				return false;
			} else {
				return true;
			}
			
		}

		
		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getChildOptionsList($cat_id) {
			
			$model      = new $this->parent_model_full_name();
			$parent_ids = $this->getParentIds($cat_id, [$cat_id], $model);
			$option_ids = ModelsOptionsList::find()->select('option_id')->where([
				'IN',
				'model_id',
				$parent_ids
			])->asArray()->all()
			;

			$options = OptionsList::find()
			                      ->where(
				                      [
					                      "IN",
					                      'id',
					                      $this->flatArray($option_ids, 'option_id')
				                      ]
			                      )
			                      ->andWhere(['model' => $this->parent_model_name . '-' . $this->model_name,])
			                      ->all()
			;

			return $options;
		}

		public function flatArray($arr, $value_name) {
			$r_arr = [];
			foreach($arr as $key => $value) {
				$r_arr[] = $value[ $value_name ];
			}

			return $r_arr;
		}

		/**
		 * @var $model  ActiveRecord
		 * @var $r_arr  array
		 * @var $cat_id integer
		 *
		 * @return array
		 */
		public function getParentIds($cat_id, $r_arr, $model) {

			/**
			 * @var $model Cats
			 */
			$this_model   = $model::findOne(['id' => $cat_id]);
			$parent_model = $model::findOne(['id' => $this_model->parent->id]);
			$parent_id    = $parent_model->parent->id;
			if($parent_id != 0) {
				$r_arr = ArrayHelper::merge($r_arr, $this->getParentIds($parent_id, $r_arr, $model));
			}
			else {
				$r_arr[] = $parent_model->id;
			}

			return $r_arr;
		}
		/**
		 * Получаем значение параметра конкретной модели по алиасу
		 * @param $alias string
		 * @return mixed
		 */
		public function getChildOptionValueByAlias($alias, $relations_model_name = 'Cats-Items') {
			/**
			 * @var $optionList OptionsList
			 * @var $option Options
			 */
			$optionList = OptionsList::find()->where(['alias'=>$alias])->one();
			$option = ChildOptions::find()
			                 ->where(
				                 [
					                 'model_id'  => $this->owner->id,
					                 'model'     => $relations_model_name,
					                 'option_id' => $optionList->id
				                 ]
			                 )->one();
			if(in_array($optionList->type->alias, MyHelper::TYPES_WITH_PRESET_ARRAY)) {
				$return = $optionList->preset->value($option->value);
			}
			elseif(in_array($optionList->type->alias, MyHelper::TYPES_WITH_MULTIPLE_PRESET_ARRAY)) {
				$optionM = ChildOptionMultiple::find()->where( [ 'value' => $option->value ] )->one();
				$return = $optionList->preset->value($optionM->value);
			}
			else {
				$return = $option->value;
			}
			return $return;
			
		}
	}