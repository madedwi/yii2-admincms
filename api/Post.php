<?php
namespace admin\api;

use Yii;
use admin\models\Post as PostModel;
use admin\models\search\PostSearch;

class Post extends \admin\components\PostAPI
{

    public function __construct(){
        parent::__construct();
        $this->baseModel = new PostModel();
    }

    public static function getPosts($condition = NULL, $limit = 5){
        $self = Post::getInstance();
        $postSearch = new PostSearch();
        $params = [];
        if(is_array($condition) && !empty($condition)){
            $params = $self->__parseRequestQuery($condition);
        }else if(is_string($condition) && !empty($condition)){
            $params = $condition;
        }

        $params = ['PostSearch' => $params];

        $postDataProvider = $postSearch->clientSearch($params, $limit);
        return $postDataProvider;
    }

}
