<?php
	
	namespace porcelanosa\yii2options\assets;
	
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
			'porcelanosa\yii2options\assets\VuejsAsset',
			'porcelanosa\yii2options\assets\VueResourceAsset',
			'porcelanosa\yii2options\assets\SortablejsAsset',
			'yii\jui\JuiAsset'
		];
		
	}
