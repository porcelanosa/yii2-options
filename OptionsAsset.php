<?php
	
	namespace porcelanosa\yii2options;
	
	use Yii;
	use yii\web\AssetBundle;
	
	class OptionsAsset extends AssetBundle {
		public $sourcePath = '@vendor/porcelanosa/yii2-options/assets';
		public $js = [
			'options.js',
		];
		public $css = [
			'options.css'
		];
		public $depends = [
			'yii\web\JqueryAsset',
			'yii\jui\JuiAsset'
		];
		
	}
