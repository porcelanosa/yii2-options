<?php
	
	namespace porcelanosa\yii2Options;

use Yii;
use yii\web\AssetBundle;

class OptionsAsset extends AssetBundle
{
    public $sourcePath = '@app/behaviors/options/assets';
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
