<?php

namespace admin\controllers;

use Yii;

class ClientController extends \yii\web\Controller
{

    private static $adminModule ;
    public function beforeAction($action){
        self::$adminModule = Yii::$app->controller->module;
        $this->layout = $this->layout = self::$adminModule->publicLayout;
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        
        return $this->render('index');
    }

}
