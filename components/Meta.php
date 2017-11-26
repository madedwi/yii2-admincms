<?php
namespace admin\components;

use Yii;

class Meta extends \yii\base\Component{

    public $__registeredMeta = [];

    public $defaultMeta = [];

    public $mappedMeta = [];

    private $useHttpEquiv = [
        'Content-Language',
        'Content-Type',
        'refresh'
    ];

    private $defaultMap = [
        'seo_description' => 'description',
        'seo_title' => 'title',
        'seo_keyword' => 'keyword',
        'header_img' => 'image'
    ];

    private $useOGProperty = [
        'type', 'title', 'description', 'image'
    ];

    private $useTwitterCard = [
        'title', 'description', 'image'
    ];

    public function init(){

    }

    public function setMetaFromPage($page, $map = []){
        $map = array_merge($this->defaultMap, $map);

        if(in_array($page->type, ['post', 'page'])){
            Yii::$app->view->registerMetaTag([
                'property' => "twitter:type",
                'content' => 'article'
            ]);

            Yii::$app->view->registerMetaTag([
                'property' => "og:type",
                'content' => 'article'
            ]);
        }

        foreach ($map as $meta_page => $meta_name) {

            if($meta_name == 'image' && !empty($page->$meta_page) && strpos(\yii\helpers\Url::home(true), $page->$meta_page) === FALSE){
                $page->$meta_page = \yii\helpers\Url::home(true) . $page->$meta_page;
                $page->$meta_page = preg_replace('#/{2}#', '/', $page->$meta_page);
            }

            if(in_array($meta_name, $this->useOGProperty)){
                Yii::$app->view->registerMetaTag([
                    'property' => "og:{$meta_name}",
                    'content' => $page->$meta_page
                ]);
            }

            if(in_array($meta_name, $this->useTwitterCard)){
                Yii::$app->view->registerMetaTag([
                    'property' => "twitter:{$meta_name}",
                    'content' => $page->$meta_page
                ]);
            }

            if($meta_name == 'title'){
                Yii::$app->view->title = empty($page->$meta_page) ? $page->title : $page->$meta_page . ' - ' . Yii::$app->name;
                continue;
            }

            Yii::$app->view->registerMetaTag([
                !in_array($meta_name, $this->useHttpEquiv) ? 'name' : 'http-equiv' => $meta_name,
                'content' => $page->$meta_page
            ]);

        }
    }



}
