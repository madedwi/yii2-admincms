<?php
namespace admin\components;

use Yii;
use admin\models\Terms;

class PostAPI extends API{

    protected $baseModel = NULL;

    protected function __parseRequestQuery($conditions){

        $postMetaAttributes = array_keys($this->baseModel->getCustomAttributes());
        $postAttributes     = array_keys($this->baseModel->attributes());

        $newConditions = ['meta' => [], 'terms'=>[]];
        foreach ($conditions as $key => $value) {
            if(in_array($key, $postMetaAttributes)){
                $newConditions['meta'][$key] = $value;
            }else if($key == Terms::TYPE_CATEGORY){
                $newConditions['terms_search']['category'] = $value;
            }else if($key == Terms::TYPE_TAG){
                $newConditions['terms_search']['tag'] = $value;
            }else{
                $newConditions[$key] = $value;
            }
        }

        return $newConditions;

    }


}
