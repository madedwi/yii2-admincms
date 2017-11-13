<?php

namespace admin\controllers;

use Yii;
use yii\web\Controller;
use admin\models\LoginForm;
use yii\filters\VerbFilter;

/**
 * Default controller for the `administrator` module
 */
class DashboardController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className()
            ],
            'admin' => [
                'class' => \admin\behaviors\AdminBehavior::className(),
                'except' => ['login', 'logout'],
                'only' => ['index']

            ]
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        // Yii::$app->user->logout();
        if(Yii::$app->user->isGuest) {
            return $this->redirect(['/administrator/login']);
        }
        return $this->render('index');
    }

    public function actionLogin(){
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $this->layout = 'login';
        $model = new LoginForm();
        if($model->load(Yii::$app->request->post()) && $model->login()){
            Yii::$app->session->setFlash('login-flash', ['status'=>'success', 'message'=>'Welcome ' . Yii::$app->user->identity->firstname ]);
            return $this->redirect(['/administrator']);
        }

        return $this->render('login', [
            'model' => $model,
            'errors' => $model->firstErrors

        ]);
    }

    public function actionLogout(){
        Yii::$app->user->logout();
        return $this->redirect(['/administrator/login']);
    }
}
