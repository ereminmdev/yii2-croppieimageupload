<?php

namespace ereminmdev\yii2\croppieimageupload;

use yii\web\AssetBundle;


class CroppieImageUploadAsset extends AssetBundle
{
    public $sourcePath = '@vendor/ereminmdev/yii2-croppieimageupload/assets';

    public $js = [
        'croppieImageUpload.js',
    ];

    public $css = [
        'croppieImageUpload.css',
    ];

    public $depends = [
        'ereminmdev\yii2\croppieimageupload\CroppieAsset',
        'ereminmdev\yii2\croppieimageupload\ExifAsset',
    ];
}
