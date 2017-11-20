<?php
namespace admin\api;

use Yii;
use admin\components\PostAPI;
use admin\models\Page as ModelPage;
use admin\models\search\PageSearch;

class Page extends PostAPI{

    public static $instance = NULL;

    public function __construct(){
        parent::__construct();
        $this->baseModel = new ModelPage();
    }

    public static function getInstance(){
        if(is_null(static::$instance)){
            static::$instance = new self();
        }

        return static::$instance;
    }

    public static function getPage(Array $filters = [], $limit=10){
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

}
