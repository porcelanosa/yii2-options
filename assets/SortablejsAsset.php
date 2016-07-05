<?php
/**
 * @link http://vuejs.org/guide/installation.html
 */

namespace porcelanosa\yii2options\assets;

use yii\web\AssetBundle;

class SortablejsAsset extends AssetBundle
{
    public $sourcePath = '@bower/Sortable';

    public $css = [
        //'foundation.min.css',
    ];

    public $js = [
	    'Sortable.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}