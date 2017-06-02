<?php

namespace ereminmdev\yii2\croppieimageupload;

use yii\web\AssetBundle;

class CroppieAsset extends AssetBundle
{
    public $sourcePath = '@vendor/npm/croppie';

    public $js = [
        YII_DEBUG ? 'croppie.js' : 'croppie.min.js',
    ];

    public $css = [
        'croppie.css',
    ];
}
