<?php
namespace admin\assets;

class AdminAsset extends \yii\web\AssetBundle{

    public $sourcePath = '@admin/template';

    public $css = [
        'http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,600,700,300',
        'http://fonts.googleapis.com/css?family=Roboto:400,300',
        'css/beyond.min.css',
        'css/animate.min.css',
        'css/custom.min.css'
    ];

    public $js = [
        'js/bootbox.min.js',
        'plugins/slimscroll/jquery.slimscroll.min.js',
        'js/skins.min.js',
        'js/beyond.min.js',
        'js/custom.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    public $publishOptions = [
        'forceCopy' => YII_ENV_DEV,
        'only' => ['css/*', 'js/*', 'plugins/slimscroll/*', 'img/*',  'img/avatars/*']
    ];

}
