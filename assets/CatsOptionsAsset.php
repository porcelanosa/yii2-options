<?php
	
	namespace porcelanosa\yii2options\assets;
	
	use Yii;
	use yii\web\AssetBundle;
	
	class CatsOptionsAsset extends AssetBundle {
		public $sourcePath = '@vendor/porcelanosa/yii2-options/assets';
		public $js = [
			'js/CatsOptions.js',
			'js/sortOptions.js',
		];
		public $css = [
			//'css/options.css'
		];
		public $depends = [
			'yii\web\JqueryAsset',
			'porcelanosa\yii2options\assets\VuejsAsset',
			'porcelanosa\yii2options\assets\VueResourceAsset',
			'porcelanosa\yii2options\assets\SortablejsAsset',
			'porcelanosa\yii2options\assets\OptionsAsset',
			'yii\jui\JuiAsset'
		];
		
	}
