<?php
namespace admin\behaviors;

use Yii;

use yii\base\Controller;

class ClientBehavior extends \yii\base\Behavior{

    private $cache;

    public function events(){
        return [
            Controller::EVENT_BEFORE_ACTION => 'clientBeforeAction'
        ];
    }

    public function clientBeforeAction(){
        $this->cache = Yii::$app->_adminFileCache;
        $this->loadClientMenu();

    }

    private function loadClientMenu(){
        Yii::$app->params['mainMenuItems'] = $this->cache->getOrSet('clientMenu', function(){
            return [];
        }, 10);

    }

}
