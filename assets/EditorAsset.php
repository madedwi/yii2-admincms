<?php
namespace admin\assets;

class EditorAsset extends \yii\web\AssetBundle{

    public $sourcePath = '@admin/template/plugins';

    public $css = [
        'bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css',
        'summernote/summernote.css',
        'bootstrap-tagsinput/bootstrap-tagsinput.css',
        'editor/editor.min.css'
    ];

    public $js = [
        'bootstrap-datepicker/js/moment.js',
        'bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js',
        'summernote/summernote.min.js',
        'bootstrap-tagsinput/bootstrap3-typeahead.min.js',
        'bootstrap-tagsinput/bootstrap-tagsinput.min.js',
        'editor/editor.js'
    ];

    public $depends = [
        // 'admin\widgets'
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    public $publishOptions = [
        'forceCopy' => YII_ENV_DEV,
        'only' => [
                    'bootstrap-datepicker/js/*',
                    'bootstrap-datepicker/css/*',
                    'bootstrap-datepicker/locales/*',
                    'summernote/*',
                    'summernote/font/*',
                    'summernote/lang/*',
                    'bootstrap-datetimepicker/css/*',
                    'bootstrap-datetimepicker/js/*',
                    'bootstrap-tagsinput/*',
                    'editor/*']
    ];
}
