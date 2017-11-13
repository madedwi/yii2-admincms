<?php
namespace admin\widgets;


class FontAwesomeAsset extends \yii\web\AssetBundle{

    public $sourcePath = '@admin/template/plugins/typeiconfont';

    public $css = [
        'typicons.min.css?d=' . date()
    ];

}
