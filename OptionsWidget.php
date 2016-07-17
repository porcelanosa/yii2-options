<?php
	namespace porcelanosa\yii2options;
	
	use porcelanosa\yii2options\assets\OptionsAsset;
	use porcelanosa\yii2options\models\OptionPresetValues;
	use porcelanosa\yii2options\models\Options;
	use porcelanosa\yii2options\models\OptionsList;
	use porcelanosa\yii2options\models\RichTexts;
	use Yii;
	use yii\base\Exception;
	use yii\base\Widget;
	use yii\db\ActiveRecord;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Json;
	use yii\helpers\Url;
	use yii\helpers\Html;
	
	
	/**
	 * Widget to Options Behavior
	 *
	 * @author Porcelanosa
	 */
	class OptionsWidget extends Widget {
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
			
			if ( $this->behavior->getOptionsList() AND is_array( $this->behavior->getOptionsList() ) ) {
				foreach ( $this->model->optionsList as $optionList ) {
					/**
					 * @var $optionList OptionsList
					 * @var $option     Options
					 */
					$option      = Options::findOne(
						[
							'model'     => $this->behavior->model_name,
							'model_id'  => $this->model->id,
							'option_id' => $optionList->id
						]
					);
					$option_name = trim( str_replace( ' ', '_', $optionList->alias ) );
					$value       = $this->behavior->getOptionValueById( $optionList->id );
					$alias       = $optionList->type->alias;
					if ( $alias == 'boolean' ) {
						$this->options_string .=
							$this->render(
								'@vendor/porcelanosa/yii2-options/views/_partials/_boolean',
								[
									'option_name' => $option_name,
									'optionList'  => $optionList,
									'value'       => $value,
								]
							);
					}
					if ( $alias == 'textinput' ) {
						$this->options_string .=
							$this->render(
								'@vendor/porcelanosa/yii2-options/views/_partials/_textinput',
								[
									'option_name' => $option_name,
									'optionList'  => $optionList,
									'value'       => $value,
								]
							);
					}
					if ( $alias == 'textarea' ) {
						$textarea = $option ? RichTexts::find()->where( [ 'option_id' => $option->id ] )->one() : NULL;
						$this->options_string .=
							$this->render(
								'@vendor/porcelanosa/yii2-options/views/_partials/_textarea',
								[
									'option_name'   => $option_name,
									'optionList'    => $optionList,
									'richTextValue' => $textarea != NULL ? $textarea->text : '',
								]
							);
					}
					if ( $alias == 'richtext' ) {
						$richText = $option ? RichTexts::find()->where( [ 'option_id' => $option->id ] )->one() : NULL;
						$this->options_string .=
							$this->render(
								'@vendor/porcelanosa/yii2-options/views/_partials/_rich_text',
								[
									'option_name'   => $option_name,
									'optionList'    => $optionList,
									'richTextValue' => $richText != NULL ? $richText->text : '',
								]
							);
					}
					if ( $alias == 'dropdown' ) {
						// получаем фабрики
						$status_preset_values =
							OptionPresetValues::find()->where( [ 'preset_id' => $optionList->preset->id ] )->orderBy( 'sort' )->all();
						// формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
						$status_preset_items = ArrayHelper::map( $status_preset_values, 'id', 'value' );
						$status_preset_items =
							ArrayHelper::merge( [ 'null' => 'Выберите ' . mb_strtolower( $optionList->name ) ], $status_preset_items );
						$this->options_string .=
							$this->render(
								'@vendor/porcelanosa/yii2-options/views/_partials/_dropdown',
								[
									'option_name'         => $option_name,
									'optionList'          => $optionList,
									'value'               => $value,
									'status_preset_items' => $status_preset_items,
								]
							);
					}
					if ( $alias == 'radiobuton_list' ) {
						
						$value = $this->behavior->getOptionValueById( $optionList->id );
						// получаем фабрики
						$status_preset_values =
							OptionPresetValues::find()->where( [ 'preset_id' => $optionList->preset->id ] )->orderBy( 'sort' )->all();
						// формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
						$status_preset_items = ArrayHelper::map( $status_preset_values, 'id', 'value' );
						$this->options_string .=
							'<label>&nbsp;' . $optionList->name . '</label>' .
							Html::radioList(
								$option_name, $value ? $value : NULL, $status_preset_items, [
									'id'    => $option_name,
									'class' => 'form-control'
								]
							);
					}
					if ( $alias == 'dropdown-multiple' ) {
						//  получаем список значений для мульти селектед
						$multipleValuesArray = $option ? $this->behavior->getOptionMultipleValueByOptionId( $option->id ) : [ ];
						// получаем фабрики
						$status_preset_values =
							OptionPresetValues::find()->where( [ 'preset_id' => $optionList->preset->id ] )->orderBy( 'sort' )->all();
						// формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
						$status_preset_items = ArrayHelper::map( $status_preset_values, 'id', 'value' );
						
						$this->options_string .=
							$this->render(
								'@vendor/porcelanosa/yii2-options/views/_partials/_dropdown_multiple',
								[
									'option_name'         => $option_name,
									'optionList'          => $optionList,
									'multipleValuesArray' => $multipleValuesArray,
									'status_preset_items' => $status_preset_items,
								]
							);
					}
					/*  checkbox list  */
					if ( $alias == 'checkboxlist-multiple' ) {
						
						//  получаем список значений для мульти селектед
						// а если нет - возвращаем пустой массив
						$multipleValuesArray = $option ? $this->behavior->getOptionMultipleValueByOptionId(
							$option->id
						) : [ ];
						// получаем список предустановленных значений
						$status_preset_values = OptionPresetValues::find()->where( [ 'preset_id' => $optionList->preset->id ] )->orderBy( 'sort' )->all();
						// формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
						$status_preset_items = ArrayHelper::map( $status_preset_values, 'id', 'value' );
						
						$this->options_string .=
							$this->render(
								'@vendor/porcelanosa/yii2-options/views/_partials/_checkboxlist_multiple',
								[
									'option_name'         => $option_name,
									'optionList'          => $optionList,
									'multipleValuesArray' => $multipleValuesArray,
									'status_preset_items' => $status_preset_items,
								]
							);
					}
					/*  IMAGE Изображение */
					if ( $alias == 'image' ) {
						$this->options_string .=
							$this->render(
								'@vendor/porcelanosa/yii2-options/views/_partials/_image',
								[
									'option_name' => $option_name,
									'optionList'  => $optionList,
									'value'       => $value,
									'this_widget' => $this,
									'behavior'    => $this->behavior,
								]
							);
					}
				}
			}
			
			$view = $this->getView();
			OptionsAsset::register( $view );
			//$view->registerJs("$('#{$this->id}').galleryManager({$opts});");
			
			$this->options['id']    = 'opt-widget-' . $this->model->id;
			$this->options['class'] = 'options';
			
			return $this->render( 'optionsWidget', [ 'options_string' => $this->options_string ] );
		}
		
	}
