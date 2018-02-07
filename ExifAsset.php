<?php

namespace ereminmdev\yii2\croppieimageupload;

use yii\web\AssetBundle;

class ExifAsset extends AssetBundle
{
    public $sourcePath = '@npm/exif-js';

    public $js = [
        'exif.js',
    ];
}
