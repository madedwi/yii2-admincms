<?php
namespace admin\api;

use Yii;
use admin\components\PostAPI;
use admin\models\Page as ModelPage;
use admin\models\search\PageSearch;
use admin\models\Terms;

class Page extends PostAPI{

    public static $instance = NULL;

    public function __construct(){
        parent::__construct();
        $this->baseModel = new ModelPage();
    }

    public static function getOne(Array $filters = []){
        $pages = static::getAll($filters,1);
        if($pages->models != null){
            $page = $pages->models[0]->toArray();
            return $page;
        }else{
            return false;
        }

    }

    public static function getAll(Array $filters = [], $limit=10){
        $self = self::getInstance();
        $pageSearch = new PageSearch();
        $params = ['PageSearch' => []];
        if(is_array($filters) && !empty($filters)){
            $params['PageSearch'] = $self->__parseRequestQuery($filters);
        }

        $postDataProvider = $pageSearch->clientSearch($params, $limit);

        return $postDataProvider;
    }

    public static function getCachedPage($cacheKey, $timeLimit = NULL, Array $filters = []){
        $dbDependency = new \yii\caching\DbDependency([
            'sql' => "SELECT MAX(modified) FROM post WHERE type='page' AND status='publish'"
        ]);

        return Yii::$app->cache->getOrSet($cacheKey, function()use($filters){
            $self = self::getInstance();
            $pageSearch = new PageSearch();
            $params = ['PageSearch' => []];
            if(is_array($filters) && !empty($filters)){
                $params['PageSearch'] = $self->__parseRequestQuery($filters);
            }
            $postDataProvider = $pageSearch->clientSearch($params, false);

            return $postDataProvider->models;
        }, $timeLimit, $dbDependency);
    }

    public static function getArchives($page, $limit = 5){
        // $filters = Yii::$app->request->queryParams;
        $filters = [];
        if($page['blog_archives'] != 'all'){
            $cat = Terms::findCategory()->where(['id' => $page['blog_archives']])->asArray()->one();
            $filters['category'] = $cat['terms_slug'];
        }
        $post = Post::getAll($filters, $limit);
        return $post;
    }

}
