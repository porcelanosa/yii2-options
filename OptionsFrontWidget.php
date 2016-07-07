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
	 * @author Porcelanosa
	 */
	class OptionsFrontWidget extends Widget {
		/** @var ActiveRecord */
		public $model;
		
		/** @var string */
		public $behaviorName;
		/** @var OptionsBehavior Model of gallery to manage */
		protected $behavior;
		
		public $options_alias_array = array();
		
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
			if(count($this->options_alias_array) == 0) {
					$options_array = $this->behavior->getOptionsList();
			}
			else {
				$options_array = OptionsList::find()->where(
					[
						'IN',
						'alias',
						$this->options_alias_array
					]
				)->all();
			}
			$r_arr = [];
			/**
			 * @var $opt OptionsList
			*/
			foreach ($options_array as $i=>$opt) {
				$r_arr[$i]['name'] = $opt->name;
				$r_arr[$i]['value'] = $this->behavior->getOptionValueByAlias($opt->alias);
			}
			
			
			return $this->render( 'optionsFrontWidget', [ 'options_array' => $r_arr ] );
		}
		
	}
