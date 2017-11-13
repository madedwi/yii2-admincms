<?php
namespace admin\helpers;

use yii\helpers\Html;

class MediaHelper {

    public static $availabelSizes = NULL;

    public function getImage($url, $options=[]){
        if(array_key_exists('size', $options)){
            $size = $options['size'];
            if(is_array($size)){
                $options['width'] = $size[0];
                $options['heigth'] = $size[1];
            }else{
                if(is_null(MediaHelper::$availabelSizes)){
                    $media = \admin\models\Media::getInstance();

                    MediaHelper::$availabelSizes = \yii\helpers\ArrayHelper::toArray($media->loadImageSizes());
                }

                if(array_key_exists($size, MediaHelper::$availabelSizes)){
                    $_url = explode('/', $url);
                    $filename = array_pop($_url);
                    $_url[] = $size;
                    $_url[] = $filename;
                    $url = implode('/', $_url);
                }
            }
        }

        return Html::img($url, $options);
    }
}
