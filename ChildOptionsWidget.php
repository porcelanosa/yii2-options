<?php
	namespace porcelanosa\yii2options;
	
	use porcelanosa\yii2options\assets\OptionsAsset;
	use porcelanosa\yii2options\components\helpers\MyHelper;
	use porcelanosa\yii2options\models\ChildOptions;
	use porcelanosa\yii2options\models\OptionPresetValues;
	use porcelanosa\yii2options\models\Options;
	use porcelanosa\yii2options\models\RichTexts;
	use Yii;
	use yii\base\Exception;
	use yii\base\Widget;
	use yii\db\ActiveRecord;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Json;
	use yii\helpers\Url;
	use yii\helpers\Html;
	
	use vova07\imperavi\Widget as Redactor;
	
	/**
	 * Widget to Options Behavior
	 *
	 * @author Porcelanosa
	 */
	class ChildOptionsWidget extends Widget {
		/** @var ActiveRecord */
		public $model;
		
		/** @var string */
		public $behaviorName;
		
		/** @var OptionsBehavior Model of gallery to manage */
		protected $behavior;
		
		public $options = array();
		
		public $options_string = '';
		
		public function init() {
			parent::init();
			$this->behavior = $this->model->getBehavior( $this->behaviorName );
			//$this->registerTranslations();
		}
		
		public function registerTranslations() {
			$i18n                                   = Yii::$app->i18n;
			$i18n->translations['galleryManager/*'] = [
				'class'          => 'yii\i18n\PhpMessageSource',
				'sourceLanguage' => 'en-US',
				'basePath'       => '@zxbodya/yii2/galleryManager/messages',
				'fileMap'        => [ ],
			];
		}
		
		
		/** Render widget */
		public function run() {
			
			$model  = $this->behavior->owner;
			$parent_relation = $this->behavior->parent_relation;
			//$cat_id = $model->cat->id;
			$cat_id = $model->{$parent_relation}->id;
			/**
			 * Формируем поля для параметров
			 *
			 * @var $optionList \porcelanosa\yii2options\models\OptionsList
			 */
			if($this->behavior->getChildOptionsList($cat_id) AND is_array($this->behavior->getChildOptionsList($cat_id))) {
				foreach($this->behavior->getChildOptionsList($cat_id) as $optionList) {
					$option      = ChildOptions::findOne([
						'model'     => $this->behavior->model_name,
						'model_id'  => $model->id,
						'option_id' => $optionList->id
					]);
					$option_name = trim(str_replace(' ', '_', $optionList->alias));
					$value       = $this->behavior->getChildOptionValueById($optionList->id);
					
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
						$multipleValuesArray = $this->behavior->getChildOptionMultipleValueByOptionId($option->id);
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
						$multipleValuesArray = $this->behavior->getChildOptionMultipleValueByOptionId($option->id);
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
			
			$view = $this->getView();
			OptionsAsset::register( $view );
			
			$this->options['id']    = 'child-opt-widget-' . $this->model->id;
			$this->options['class'] = 'child-options';
			
			return $this->render( 'optionsWidget', [ 'options_string' => $this->options_string ] );
		}
		
	}
