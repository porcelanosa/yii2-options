<?php
	namespace app\behaviors\options;
	
	use app\components\helpers\MyHelper;
	use app\modules\admin\models\Cats;
	use app\modules\admin\models\ChildOptionMultiple;
	use app\modules\admin\models\ChildOptions;
	use app\modules\admin\models\ModelsOptionsList;
	use app\modules\admin\models\OptionMultiple;
	use app\modules\admin\models\OptionPresetValues;
	use app\modules\admin\models\Options;
	use app\modules\admin\models\OptionsList;
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
		
		public function events() {
			return [
				ActiveRecord::EVENT_BEFORE_UPDATE => 'saveOptions',
				//Controller::EVENT_BEFORE_ACTION   => 'fetchOptions',
				/*$this->owner::EVENT_INIT          => 'fetchOptions',
				{$this->model_name.'Controller'}::EVENT_INIT          => 'fetchOptions',*/
			];
		}
		
		
		public function fetchOptions() {
			$model  = $this->owner;
			$cat_id = $model->cat->id;
			/**
			 * Формируем поля для параметров
			 *
			 * @var $optionList OptionsList
			 */
			if($this->getChildOptionsList($cat_id) AND is_array($this->getChildOptionsList($cat_id))) {
				foreach($this->getChildOptionsList($cat_id) as $optionList) {
					$option      = ChildOptions::findOne([
						'model'     => $this->model_name,
						'model_id'  => $model->id,
						'option_id' => $optionList->id
					]);
					$option_name = trim(str_replace(' ', '_', $optionList->alias));
					$value       = $this->getChildOptionValueById($optionList->id);
					
					if($optionList->type->alias == 'boolean') {
						
						$this->options_string .=
							'<div class="checkbox">
							<label>' .
							Html::checkbox($option_name, $value ? $value : 0, [
								'id'    => $option_name,
								'class' => 'i-check'
							]) . '
									&nbsp;' . $optionList->name .
							'</label>
					    </div>';
					}
					if($optionList->type->alias == 'textinput') {
						
						$this->options_string .=
							'<label>&nbsp;' . $optionList->name . '</label>' .
							Html::textInput($option_name, $value ? $value : 0, [
								'id'    => $option_name,
								'class' => 'form-control'
							]);
					}
					if($optionList->type->alias == 'dropdown') {
						// получаем фабрики
						$status_preset_values =
							OptionPresetValues::find()->where(['preset_id' => $optionList->preset->id])->orderBy('sort')->all();
						// формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
						$status_preset_items = ArrayHelper::map($status_preset_values, 'id', 'value');
						$status_preset_items =
							ArrayHelper::merge(['null' => 'Выберите ' . mb_strtolower($optionList->name)], $status_preset_items);
						$this->options_string .=
							'<label>&nbsp;' . $optionList->name . '</label>' .
							Html::dropDownList($option_name, $value ? $value : NULL, $status_preset_items, [
								'id'    => $option_name,
								'class' => 'form-control'
							]);
					}
					if($optionList->type->alias == 'radiobuton_list') {

						// получаем фабрики
						$status_preset_values =
							OptionPresetValues::find()->where(['preset_id' => $optionList->preset->id])->orderBy('sort')->all();
						// формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
						$status_preset_items = ArrayHelper::map($status_preset_values, 'id', 'value');
						$this->options_string .=
							'<label>&nbsp;' . $optionList->name . '</label>' .
							Html::radioList($option_name, $value ? $value : NULL, $status_preset_items, [
								'id'    => $option_name,
								'class' => 'form-control'
							]);
					}
					if($optionList->type->alias == 'dropdown-multiple') {
						//  получаем список значений для мульти селектед
						$multipleValuesArray = $this->getChildOptionMultipleValueByOptionId($option->id);
						// получаем фабрики
						$status_preset_values =
							OptionPresetValues::find()->where(['preset_id' => $optionList->preset->id])->orderBy('sort')->all();
						// формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
						$status_preset_items = ArrayHelper::map($status_preset_values, 'id', 'value');
						
						$this->options_string .=
							'<label>&nbsp;' . $optionList->name . '</label>' .
							Html::dropDownList(
								$option_name,
								$multipleValuesArray,
								$status_preset_items,
								[
									'id'       => $option_name,
									'class'    => 'form-control',
									'multiple' => 'true'
								]
							);
					}
					/*  Список checkboxes  */
					if($optionList->type->alias == 'checkboxlist-multiple') {
						//  получаем список значений для мульти селектед
						$multipleValuesArray = $this->getChildOptionMultipleValueByOptionId($option->id);
						// получаем фабрики
						$status_preset_values =
							OptionPresetValues::find()->where(['preset_id' => $optionList->preset->id])->orderBy('sort')->all();
						// формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
						$status_preset_items = ArrayHelper::map($status_preset_values, 'id', 'value');
						
						$this->options_string .=
							'<label>&nbsp;' . $optionList->name . '</label>' .
							Html::checkboxList(
								$option_name,
								$multipleValuesArray,
								$status_preset_items,
								[
									'id'       => $option_name,
									'class'    => 'form-control',
									'multiple' => 'true'
								]
							);
					}
				}
			}
			echo $this->options_string;
		}
		
		
		public function saveOptions() {
			
			$model  = $this->owner;
			$cat_id = $model->cat->id;
			
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

		protected function setMultipleOptions($option_type, $option_name, $option_id) {
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
		protected function getChildOptionValueById($option_id) {
			
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
		protected function ifOptionExist($option_id) {
			
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
		protected function getChildOptionsList($cat_id) {
			
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

		protected function flatArray($arr, $value_name) {
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
		protected function getParentIds($cat_id, $r_arr, $model) {

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

	}