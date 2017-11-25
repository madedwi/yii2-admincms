<?php

namespace admin\controllers;

use Yii;
use admin\models\GeneralOptions;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\filters\AccessControl;
/**
 * ConfigurationController implements the CRUD actions for Options model.
 */
class ConfigurationController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],

            'adminBevahior' => \admin\behaviors\AdminBehavior::className()
        ];
    }

    public function actionGeneral(){
        $model = new GeneralOptions();

        if($model->load(Yii::$app->request->post()) && $model->save()){
            Yii::$app->session->setFlash('general_config_flash', ['status'=>'success', 'message'=>'Configuration has been saved']);
            return $this->refresh();
        }

        return $this->render('general', [
            'model' => $model
        ]);
    }

    public function actionFrontendMenu(){

        return $this->render('menu', [

        ]);
    }

    public function actionLayout(){
        $this->layout = 'layout-manager';
        return $this->render('layout', [

        ]);
    }

}
