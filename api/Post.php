<?php
namespace admin\api;

use Yii;
use admin\models\Post as PostModel;
use admin\models\search\PostSearch;

class Post extends \admin\components\Api
{



    private static function __parseRequestQuery($conditions){
        $postModel = new PostModel();
        $postBehavior = $postModel->getBehavior('postBehavior');
        $behaviorMeta = $postBehavior->defaultMetas;
        $customMeta   = $postModel->customMetas;

        $postMetaAttributes = array_merge($behaviorMeta, array_keys($customMeta));
        $postAttributes     = array_keys($postModel->attributeLabels());

        $newConditions = ['meta' => [], 'terms'=>[]];
        foreach ($conditions as $key => $value) {
            if(in_array($key, $postMetaAttributes)){
                $newConditions['meta'][$key] = $value;
            }else if($key == \admin\models\Terms::TYPE_CATEGORY){
                $newConditions['terms']['category'] = $value;
            }else if($key == \admin\models\Terms::TYPE_TAG){
                $newConditions['terms']['tag'] = $value;
            }else{
                $newConditions[$key] = $value;
            }
        }

        return ['PostSearch' => $newConditions];

    }

    public static function getPosts($condition = NULL, $limit = 5){
        $postSearch = new PostSearch();
        $params = [];
        if(is_array($condition) && !empty($condition)){
            $params = self::__parseRequestQuery($condition);
        }else if(is_string($condition) && !empty($condition)){
            $params = $condition;
        }

        $postDataProvider = $postSearch->clientSearch($params, $limit);
        return $postDataProvider;
    }

}
