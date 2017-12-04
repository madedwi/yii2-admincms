<?php
namespace admin\api;

use Yii;
use admin\models\Post as PostModel;
use admin\models\search\PostSearch;
use admin\models\search\CategoriesSearch;
use yii\helpers\ArrayHelper;

class Post extends \admin\components\PostAPI
{

    public function __construct(){
        parent::__construct();
        $this->baseModel = new PostModel();
    }

    protected function __parseRequestQuery($condition){
        if(array_key_exists('fpage', $condition)){
            unset($condition['fpage']);
        }

        return parent::__parseRequestQuery($condition);

    }

    public static function getOne($condition = []){
        $qp = Yii::$app->request->queryParams;
        if(array_key_exists('terms', $qp)){
            $qp['terms'] = ['category' => $qp['terms']];
        }

        $condition = array_merge($qp, $condition);

        $post = Post:: getAll($condition, 1);
        if($post != null){
            return $post[0];
        }else{
            return false;
        }

    }

    public static function getAll($condition = NULL, $limit = 5){
        $self = Post::getInstance();
        $postSearch = new PostSearch();
        $params = [];
        if(is_array($condition) && !empty($condition)){
            $params = $self->__parseRequestQuery($condition);
        }else if(is_string($condition) && !empty($condition)){
            $params = $condition;
        }
        $params = ['PostSearch' => $params];
        $posts = $postSearch->clientSearch($params, $limit);
        foreach ($posts as $index => $post) {
            $posts[$index] = $post->toArray([], ['author', 'postTerms']);
        }


        return $posts;
    }

    public static function getCategories($filters = NULL){

        $dependency = new \yii\caching\DbDependency(['sql' => 'SELECT MAX(modified) FROM post_terms ORDER BY modified DESC LIMIT 2']);

        $cache = Yii::$app->_adminFileCache;

        return $cache->getOrSet('categories', function()use($filters){
            $self = Post::getInstance();
            $categorySearch = new CategoriesSearch();
            $categoryDataProvider = $categorySearch->clientSearch(['CategoriesSearch' => $filters], false);

            $array = [];
            foreach ($categoryDataProvider->models as $model) {

                $array[] = ArrayHelper::toArray($model, [
                    'admin\models\search\CategoriesSearch' => [
                        'id', 'terms', 'terms_slug',
                        'childs' => function($model){
                            $childs = [];
                            foreach ($model->childsCategory as $cld) {
                                $childs[] = ArrayHelper::toArray($cld, [
                                    'admin\models\Terms' => [
                                        'id', 'terms', 'terms_slug',
                                        'postCount' => function($model){
                                            return count($model->posts);
                                        }
                                    ]
                                ]);
                            }

                            return $childs;
                        },
                        'postCount' => function($model){
                            return count($model->posts);
                        },
                        'formattedSlug'
                    ]
                ]);


            }
            return $array;
        }, 2 * 3600, $dependency);

    }

    public static function addViewCounter(Array $post){
        $connection = Yii::$app->db;
        $viewCounter = empty($post['viewCounter']) ? 0 : $post['viewCounter'];
        $viewCounter = intval($viewCounter) + 1;
        $query = "REPLACE INTO post_meta (post_id, metakey, value) VALUES (:post_id, 'viewCounter', :viewCounter)";
        $command = $connection->createCommand($query);
        $command->bindValue(':post_id', $post['id']);
        $command->bindValue(':viewCounter', $viewCounter);
        if(!$command->execute()){
            throw new Exception("Cannot update view counter. [POST API : ]");
        }
    }

    public static function getShortDescription($text, $maxChar = 255){
        return \admin\helpers\String::truncate($text, $maxChar, '');
    }

}
