<?php
/**
 * @link http://vuejs.org/guide/installation.html
 */

namespace porcelanosa\yii2options\assets;

use yii\web\AssetBundle;

class VuejsAsset extends AssetBundle
{
    public $sourcePath = '@bower/vue/dist';

    public $css = [
        //'foundation.min.css',
    ];

    public $js = [
        'vue.min.js',
    ];

    public $depends = [
        //'yii\web\JqueryAsset',
    ];
}

class VueResourceAsset extends AssetBundle
{
    public $sourcePath = '@bower/vue-resource/dist';

    public $css = [
        //'foundation.min.css',
    ];

    public $js = [
        'vue-resource.min.js',
    ];

    public $depends = [
        'app\assets\VuejsAsset',
    ];
}
