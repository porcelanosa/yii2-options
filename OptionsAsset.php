<?php
	
	namespace porcelanosa\yii2options;
	
	use Yii;
	use yii\web\AssetBundle;
	
	class OptionsAsset extends AssetBundle {
		public $sourcePath = '@vendor/porcelanosa/yii2-options/assets';
		public $js = [
			'js/options.js',
			'js/optionsPreset.js',
			'js/Sortable.js',
		];
		public $css = [
			'css/options.css'
		];
		public $depends = [
			'yii\web\JqueryAsset',
			'\app\assets\VuejsAsset',
			'\app\assets\VueResourceAsset',
			'yii\jui\JuiAsset'
		];
		
	}
