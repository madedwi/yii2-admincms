<?php
namespace admin\widgets;

use Yii;

class Icon {

    public static function FA($class, $size='', $use_spin = false){

        FontAwesomeAsset::register(Yii::$app->view);

        $class .= !empty($size) ? " fa-{$size}" : '';
        $class .= ($use_spin==true) ? ' fa-spin' : '';

        return '<i class="fa fa-'.$class.'"></i>';
    }

    public static function glyph($class){
        return ' <i class="glyphicon glyphicon-'.$class.'"></i> ';
    }

    public static function typicon($class){
        TypeIconAsset::register(Yii::$app->view);
        return ' <i class="typcn typcn-'.$class.'"></i> ';
    }
}
