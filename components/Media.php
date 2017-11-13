<?php
namespace admin\components;


use Yii;
use yii\helpers\ArrayHelper;

class Media extends \yii\base\Component{

    public function loadImage($imgUrl, $size=NULL){
        if($size == NULL){
            return $imgUrl;
        }else{

        }
    }

    public function getImageSizes(){
        //{"large":{"mwidth":1200,"mheight":720},"medium":{"mwidth":720,"mheight":480},"small":{"mwidth":480,"mheight":260},"thumb":{"mwidth":150,"mheight":150}}
        $defaults = [
            /*
             size_key => [
                'mwidth' => maximum of image width,
                'mheight' => maximum of image height,
                'method' => center_crop | add_white_space | not_set
             ]
             */
            'large' => [
                'mwidth' => 1200, 'mheight' => 720
            ],
            'medium' => [
                'mwidth' => 720, 'mheight' => 480
            ],
            'small' => [
                'mwidth' => 480, 'mheight' => 260
            ],
            'thumb' => [
                'mwidth' => 150, 'mheight' => 150
            ]
        ];

        $from_client = ArrayHelper::getValue(Yii::$app->params, 'image_sizes_setting', []);
        $sizes = json_encode(array_merge($defaults, $from_client));

        return json_decode($sizes);
    }
}
