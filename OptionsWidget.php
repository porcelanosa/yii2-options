<?php
	namespace porcelanosa\yii2options;
	
	use porcelanosa\yii2options\assets\OptionsAsset;
	use porcelanosa\yii2options\components\helpers\MyHelper;
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
			/*if ($this->apiRoute === null) {
				throw new Exception('$apiRoute must be set.', 500);
			}*/
			
			/*$images = array();
			foreach ($this->behavior->getImages() as $image) {
				$images[] = array(
					'id' => $image->id,
					'rank' => $image->rank,
					'name' => (string)$image->name,
					'description' => (string)$image->description,
					'preview' => $image->getUrl('preview'),
				);
			}
	
			$baseUrl = [
				$this->apiRoute,
				'type' => $this->behavior->type,
				'behaviorName' => $this->behaviorName,
				'galleryId' => $this->behavior->getGalleryId()
			];
	
			$opts = array(
				'hasName' => $this->behavior->hasName ? true : false,
				'hasDesc' => $this->behavior->hasDescription ? true : false,
				'uploadUrl' => Url::to($baseUrl + ['action' => 'ajaxUpload']),
				'deleteUrl' => Url::to($baseUrl + ['action' => 'delete']),
				'updateUrl' => Url::to($baseUrl + ['action' => 'changeData']),
				'arrangeUrl' => Url::to($baseUrl + ['action' => 'order']),
				'nameLabel' => Yii::t('galleryManager/main', 'Name'),
				'descriptionLabel' => Yii::t('galleryManager/main', 'Description'),
				'photos' => $images,
			);
	
			$opts = Json::encode($opts);*/
			
			if ( $this->behavior->getOptionsList() AND is_array( $this->behavior->getOptionsList() ) ) {
				foreach ( $this->model->optionsList as $optionList ) {
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
							Html::checkbox( $option_name, $value ? $value : 0, [
								'id'    => $option_name,
								'class' => 'i-check'
							] ) . '
									&nbsp;' . $optionList->name .
							'</label>
					    </div>';
					}
					if ( $optionList->type->alias == 'textinput' ) {
						
						$this->options_string .=
							'<label>&nbsp;' . $optionList->name . '</label>' .
							Html::textInput( $option_name, $value ? $value : 0, [
								'id'    => $option_name,
								'class' => 'form-control'
							] );
					}
					if ( $optionList->type->alias == 'textarea' ) {
						
						$this->options_string .=
							'<label>&nbsp;' . $optionList->name . '</label>' .
							Html::textarea( $option_name, $value ? $value : 0, [
								'id'    => $option_name,
								'class' => 'form-control'
							] );
					}
					if ( $optionList->type->alias == 'richtext' ) {
						$richText = RichTexts::findOne( [ 'option_id' => $optionList->id ] );
						
						$this->options_string .=
							'<label>&nbsp;' . $optionList->name . '</label>' .
							Html::textarea( $option_name, $richText->text, [
								'id' => $option_name,
								//'class' => 'form-control'
							] ) .
							Redactor::widget( [
								'selector' => '#' . $option_name,
								'settings' => [
									'lang'      => 'ru',
									'minHeight' => 200,
									'plugins'   => [
										'clips',
										'fullscreen'
									]
								]
							] );
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
							Html::dropDownList( $option_name, $value ? $value : NULL, $status_preset_items, [
								'id'    => $option_name,
								'class' => 'form-control'
							] );
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
							Html::radioList( $option_name, $value ? $value : NULL, $status_preset_items, [
								'id'    => $option_name,
								'class' => 'form-control'
							] );
					}
					if ( $optionList->type->alias == 'dropdown-multiple' ) {
						//  получаем список значений для мульти селектед
						$multipleValuesArray = $this->behavior->getOptionMultipleValueByOptionId( $option->id );
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
						$multipleValuesArray = $this->behavior->getOptionMultipleValueByOptionId( $option->id );
						// получаем фабрики
						$status_preset_values =
							OptionPresetValues::find()->where( [ 'preset_id' => $optionList->preset->id ] )->orderBy( 'sort' )->all();
						// формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
						$status_preset_items = ArrayHelper::map( $status_preset_values, 'id', 'value' );
						
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
					/*  Изображение */
					if ( $optionList->type->alias == 'image' ) {
						if ( MyHelper::IFF( $value ) ) {
							$this->options_string .= Html::img( $value, [ 'style' => 'width: 100px; height:auto;' ] );
							$delimage_link_anchor = Yii::t('app', 'Delete image');
							$this->options_string .= <<<HTML
							<br>
							<a href="" id="delimg-{$option_name}-{$this->model->id}">$delimage_link_anchor</a>
							<br>
HTML;
							$delimage_script = <<<JS
				$("#delimg-{$option_name}-{$this->model->id}").on('click', function() {
				  
				})
JS;
							$view2 = $this->getView();
							$view2->registerJs($delimage_script, $view2::POS_READY, "delimg-{$option_name}-{$this->model->id}");

						}
						
						$this->options_string .=
							'<label>&nbsp;' . $optionList->name . '</label>' .
							Html::fileInput(
								$option_name
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
