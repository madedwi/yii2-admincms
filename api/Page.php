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

    public static function getPage(Array $filters = [], $limit=10){
        $self = self::getInstance();
        $pageSearch = new PageSearch();
        $params = ['PageSearch' => []];
        if(is_array($filters) && !empty($filters)){
            $params['PageSearch'] = $self->__parseRequestQuery($filters);
        }

        $postDataProvider = $pageSearch->clientSearch($params, $limit);

        if(count($postDataProvider->models) == 1){
            $page = $postDataProvider->models[0];
            if($page->layout == 'archives'){
                $filters = [];
                if($page->blog_archives != 'all'){
                    if(($tag = Terms::findOne(['id'=>$page->blog_archives, 'type'=>Terms::TYPE_TAG])) !== null){
                        $filters = ['tag' => $tag->terms_slug];
                    }else if(($category = Terms::findOne(['id'=>$page->blog_archives, 'type'=>Terms::TYPE_CATEGORY])) !== null){
                        $filters = ['category' => $category->terms_slug];
                    }
                }
                $contents = Post::getPosts($filters);
                $contents->prepare();
                $page->setCustomAttribute('archived_contents',      $contents->models);
                $page->setCustomAttribute('archived_shown',         $contents->count);
                $page->setCustomAttribute('archived_total',         $contents->totalCount);
                $page->setCustomAttribute('archived_pagination',    $contents->pagination);
            }
            $postDataProvider->setModels([0=>$page]);
        }

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
