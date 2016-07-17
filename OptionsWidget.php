<?php
	namespace porcelanosa\yii2options;
	
	use porcelanosa\yii2options\assets\OptionsAsset;
	use porcelanosa\yii2options\components\helpers\MyHelper;
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
	
	use vova07\imperavi\Widget as Redactor;
	
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
					$option      = Options::findOne(
						[
							'model'     => $this->behavior->model_name,
							'model_id'  => $this->model->id,
							'option_id' => $optionList->id
						]
					);
					/**
					 * @var $optionList OptionsList
					 * @var $option Options
					 */
					$option      = Options::findOne( [
						'model'     => $this->behavior->model_name,
						'model_id'  => $this->model->id,
						'option_id' => $optionList->id
					] );
					$option_name = trim( str_replace( ' ', '_', $optionList->alias ) );
					$value       = $this->behavior->getOptionValueById( $optionList->id );
					
					if ( $optionList->type->alias == 'boolean' ) {
						
						$this->options_string .=
							'<div class="checkbox">
							<label>' .
							Html::checkbox(
								$option_name, $value ? $value : 0, [
								'id'    => $option_name,
								'class' => 'i-check'
							]
							) . '
									&nbsp;' . $optionList->name .
							'</label>
					    </div>';
					}
					if ( $optionList->type->alias == 'textinput' ) {
						
						$this->options_string .=
							'<label>&nbsp;' . $optionList->name . '</label>' .
							Html::textInput(
								$option_name, $value ? $value : 0, [
								'id'    => $option_name,
								'class' => 'form-control'
							]
							);
					}
					if ( $optionList->type->alias == 'textarea' ) {
						
						$textarea = RichTexts::find()->where( [ 'option_id' => $option->id ] )->one();
						$this->options_string .=
							$this->render(
							'@vendor/porcelanosa/yii2-options/views/_partials/_textarea',
							[
								'option_name'   => $option_name,
								'optionList'    => $optionList,
								'richTextValue' => $textarea->text,
							]
						);
					}
					if ( $optionList->type->alias == 'richtext' ) {
						$richText      = RichTexts::find()->where( [ 'option_id' => $option->id ] )->one();
						$richTextValue = $richText != NULL ? $richText->text : '';
						$this->options_string .=
							$this->render(
								'@vendor/porcelanosa/yii2-options/views/_partials/_rich_text',
								[
									'option_name'   => $option_name,
									'optionList'    => $optionList,
									'richTextValue' => $richTextValue,
								]
							);
					}
					if ( $optionList->type->alias == 'dropdown' ) {
						// получаем фабрики
						$status_preset_values =
							OptionPresetValues::find()->where( [ 'preset_id' => $optionList->preset->id ] )->orderBy( 'sort' )->all();
						// формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
						$status_preset_items = ArrayHelper::map( $status_preset_values, 'id', 'value' );
						$status_preset_items =
							ArrayHelper::merge( [ 'null' => 'Выберите ' . mb_strtolower( $optionList->name ) ], $status_preset_items );
						$this->options_string .=
							'<label>&nbsp;' . $optionList->name . '</label>' .
							Html::dropDownList(
								$option_name, $value ? $value : NULL, $status_preset_items, [
								'id'    => $option_name,
								'class' => 'form-control'
							]
							);
					}
					if ( $optionList->type->alias == 'radiobuton_list' ) {
						
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
					if ( $optionList->type->alias == 'dropdown-multiple' ) {
						//  получаем список значений для мульти селектед
						if ( $option ) {
							$multipleValuesArray = $this->behavior->getOptionMultipleValueByOptionId( $option->id );
						} else {
							$multipleValuesArray = [ ];
						}
						// получаем фабрики
						$status_preset_values =
							OptionPresetValues::find()->where( [ 'preset_id' => $optionList->preset->id ] )->orderBy( 'sort' )->all();
						// формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
						$status_preset_items = ArrayHelper::map( $status_preset_values, 'id', 'value' );
						
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
					if ( $optionList->type->alias == 'checkboxlist-multiple' ) {
						
						//  получаем список значений для мульти селектед
						$multipleValuesArray = ( $option ) ? $this->behavior->getOptionMultipleValueByOptionId( $option->id ) : [ ];
						$multipleValuesArray = $this->behavior->getOptionMultipleValueByOptionId(
							$optionList->id
						);
						// получаем фабрики
						$status_preset_values =
							OptionPresetValues::find()->where( [ 'preset_id' => $optionList->preset->id ] )->orderBy( 'sort' )->all();
						// формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
						$status_preset_items = ArrayHelper::map( $status_preset_values, 'id', 'value' );
						
						$this->options_string .= <<<HTML
						<div style="margin-bottom: 20px; padding: 5px; border: 1px solid rgba(166, 166, 166, 0.71)">
							<label>&nbsp;$optionList->name</label><br>
HTML;
						
						$this->options_string .=	Html::checkboxList(
								$option_name,
								$multipleValuesArray,
								$status_preset_items,
								[
									'id'          => $option_name,
									'class'       => 'form-control',
									'multiple'    => 'true',
									'itemOptions' => [ 'class' => 'i-check', 'style' => 'display:inline-block' ]
								]
							);
						$this->options_string .= <<<HTML
							</div>
HTML;
					}
					/*  IMAGE Изображение */
					if ( $optionList->type->alias == 'image' ) {
						
						$this->options_string .= <<<HTML
						<div style="margin-bottom: 20px; padding: 5px; border: 1px solid rgba(166, 166, 166, 0.71)">
							<label>&nbsp;$optionList->name</label><br>
HTML;
						
						// Если есть изображение, то показываем его Show image if exist
						if ( MyHelper::IFF( $value ) ) {
							$this->options_string .= Html::img(
								$value, [
								'style' => 'width: 100px; height:auto;',
								'id'    => "img-{$option_name}-{$this->model->id}",
							]
							);
							
							$delimage_link_anchor = Yii::t( 'app', 'Delete image' );
							$this->options_string .= <<<HTML
							<div id="linkblock-{$option_name}-{$this->model->id}">
							<a href="" id="delimg-{$option_name}-{$this->model->id}">$delimage_link_anchor</a>
							</div>
HTML;
							$delimage_script = <<<JS
								
								$("#delimg-{$option_name}-{$this->model->id}").on('click', function(e) {
								    e.preventDefault();
								    var option_id = "{$optionList->id}"; 
								    var model_id = "{$this->model->id}"; 
								    var model_name = "{$this->behavior->model_name}"; 
								    var url = '/options/delimage';
								    $.ajax(
								        url,
								        {
								            type: 'POST',
								            dataType: 'json',
								            data: {model_id: model_id, model_name: model_name, option_id: option_id},
								            success: function(data) {
								              if(data.success) {
								                  $("#img-{$option_name}-{$this->model->id}").remove();
								                  $("#linkblock-{$option_name}-{$this->model->id}").remove();
								              }
								            }
								        }
								    )
								})
JS;
							$view            = $this->getView();
							$view->registerJs( $delimage_script, $view::POS_READY, "delimg-{$option_name}-{$this->model->id}" );
							
						}
						
						$this->options_string .=
							Html::fileInput(
								$option_name
							);
						
						$this->options_string .= <<<HTML
							</div>
HTML;
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
