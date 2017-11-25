<?php

namespace admin\controllers;

use Yii;

class ClientController extends \yii\web\Controller
{

    public function behaviors(){
        return [
            'clientBehavior' => [
                'class' => \admin\behaviors\ClientBehavior::className()
            ]
        ];
    }

    private static $adminModule ;
    public function beforeAction($action){
        self::$adminModule = Yii::$app->controller->module;
        $this->layout = self::$adminModule->publicLayout;
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionPage(){

        return $this->render(self::$adminModule->pageView);
    }

    public function actionPost(){
        return $this->render(self::$adminModule->postView);
    }

}
