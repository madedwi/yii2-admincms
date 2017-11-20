<?php
namespace admin\api;

use Yii;
use admin\models\Post as PostModel;
use admin\models\search\PostSearch;

class Post extends \admin\components\Api
{

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
