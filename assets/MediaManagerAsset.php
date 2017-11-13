<?php
namespace admin\assets;

class MediaManagerAsset extends \yii\web\AssetBundle{


    public $sourcePath = '@admin/template/plugins/media-manager';

    public $css = [
        'media-manager.min.css',
    ];

    public $js = [
        'media-manager.js',
    ];

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset'
    ];

    public $publishOptions = [
            'forceCopy' => YII_ENV_DEV
    ];
}
