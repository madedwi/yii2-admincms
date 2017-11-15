<?php
namespace admin\behaviors;

use Yii; 
use yii\base\Controller;

class AdminBehavior extends \yii\base\Behavior{

    public $only;

    public $except;

    public $addBeforeAction;

    public function events(){
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction',
        ];
    }

    public function beforeAction($eventAction){

        $isAdminitratorUser = !Yii::$app->user->isGuest && in_array(Yii::$app->user->identity->type, array_keys(Yii::$app->getModule('administrator')->params['default_user_type']));

        if(is_object($this->addBeforeAction) && ($this->addBeforeAction instanceof \Closure)){
            $bAction = $this->addBeforeAction;
            $bAction($eventAction->action);
        }

        if(!is_null($this->except)){
            if(in_array($eventAction->action->id, $this->except)){
                return true;
            }
        }

        if(!is_null($this->only)){
            $result = true;
            if(in_array($eventAction->action->id, $this->only)){

                if(!$isAdminitratorUser){
                    return $eventAction->action->controller->redirect(['/administrator/login']);
                }

                Yii::setAlias('@adminUrl', Yii::getAlias('@web/administrator'));
                $eventAction->action->controller->layout = '@admin/views/layouts/main';
            }
        }

        return true;
    }




}
