<?php

namespace admin\controllers;

use Yii;
use admin\models\User;
use admin\models\search\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


use yii\filters\AccessControl;
/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
            'adminBehavior' => \admin\behaviors\AdminBehavior::className()
        ];
    }

    public function actionAjax(){
        $mode = Yii::$app->request->post('mode');
        if(empty($mode)){
            $mode = Yii::$app->request->get('mode');
        }
        $result = ['status'=>false, 'data'=>[], 'message'=>'Invalid request!', 't'=>Yii::$app->request->csrfParam, 'v'=>Yii::$app->request->csrfToken];
        switch ($mode) {
            case 'update-status':
                    $r = $this->updateStatus();
                    $result = array_merge($result, $r);
                break;

            default:

                break;
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $result;
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    // =========================================================================
    // USERS
    // =========================================================================
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $_userTypes = $searchModel->userTypes;
        if(!is_null($_userTypes)){
            $userTypes = array_merge(Yii::$app->getModule('administrator')->params['default_user_type'], $_userTypes);
        }else{
            $userTypes = Yii::$app->getModule('administrator')->params['default_user_type'];
        }


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'userTypes' => $userTypes
        ]);
    }

    public function actionCreate()
    {
        $model = new User();
        $model->scenario = User::SCENARIO_REGISTER;
        return $this->form($model);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = User::SCENARIO_UPDATE;
        return $this->form($model);
    }

    private function form($model){


        $_userTypes = $model->userTypes;
        if(!is_null($_userTypes)){
            $userTypes = array_merge(Yii::$app->getModule('administrator')->params['default_user_type'], $_userTypes);
        }else{
            $userTypes = Yii::$app->getModule('administrator')->params['default_user_type'];
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['user/']);
        } else {
            return $this->render('form', [
                'model' => $model,
                'userTypes' => $userTypes
            ]);
        }
    }

    public function updateStatus(){
        $id = Yii::$app->request->post('referrence');
        $status = Yii::$app->request->post('target');
        if (($model = User::findOne($id)) !== null) {
            if($model->updateStatus($status)){
                return ['status'=>true, 'data'=>[], 'message'=>'User status updated'];
            }

            return ['status'=>false, 'data'=>[], 'message'=>'Can not update user status [ '.json_encode($model->firstErrors).']'];
        }else{
            return ['status'=>false, 'data'=>[], 'message'=>'Invalid User ID.'];
        }

    }

    public function actionUserBulk(){
        $bulkAction = Yii::$app->request->post('bulk_action');
        $bulkID     = Yii::$app->request->post('bulk_id');
        try {
            switch ($bulkAction) {
                case 'active':
                        $model = new User();
                        $model->updateStatus(User::STATUS_ACTIVE, $bulkID);
                    break;
                case 'notactive' :
                        $model = new User();
                        $model->updateStatus(User::STATUS_SUSPEND, $bulkID);
                    break;
                case 'delete' :
                        $model = new User();
                        $model->updateStatus(User::STATUS_DELETED, $bulkID);
                    break;
                default:
                    throw new \Exception("Invalid action ID");
                    break;
            }

            Yii::$app->session->setFlash('usersFlash', ['status'=>'success', 'message'=>'Bulk action has been executed']);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('usersFlash', ['status'=>'error', 'message'=>$e->getMessage()]);
        }

        $this->redirect(['user/']);
    }

    // =========================================================================
    // END USERS
    // =========================================================================

    // =========================================================================
    // USER TYPES
    // =========================================================================

    public function actionUserTypes(){
        $model = new User();
        $userTypes = $model->userTypes;
        if(is_null($userTypes)){
            $allModels = null;
        }else{
            $allModels = [];
            foreach ($userTypes as $key => $value) {
                $allModels[] = ['key' => $key, 'type'=>$value];
            }
        }


        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $allModels,
            // 'sort' => [
            //     'attributes' => ['key', 'type'],
            // ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $this->render('user_type', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionFormUserType(){
        $model = new User();
        $input = Yii::$app->request->post('UserType');
        $typeKey    = $input['key'];
        $typeValue  = $input['type'];
        if($model->saveUserType($typeKey, $typeValue)){
            Yii::$app->session->setFlash('userTypeFlash', ['status'=>'success', 'message'=>'Types has been saved.']);
        }else{
            Yii::$app->session->setFlash('userTypeFlash', ['status'=>'error', 'message'=>json_encode($model->firstErrors)]);
        }
        return $this->redirect(['user-types']);
    }

    public function actionUserTypeBulk(){
        $bulkAction = Yii::$app->request->post('bulk_action');
        $bulkID     = Yii::$app->request->post('bulk_id');

        try {
            switch ($bulkAction) {
                case 'delete':
                        $model = new User();
                        $model->deleteUserType($bulkID);
                    break;

                default:
                        throw new \Exception("Invalid action ID");
                    break;
            }
            Yii::$app->session->setFlash('userTypeFlash', ['status'=>'success', 'message'=>'Bulk action has been executed']);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('userTypeFlash', ['status'=>'error', 'message'=>$e->getMessage()]);
        }

        $this->redirect(['user-types']);
    }

    // =========================================================================
    // END USER TYPES
    // =========================================================================

    // =========================================================================
    // ACCESS CONTROL
    // =========================================================================

    public function actionAccessControl(){
        return $this->render('access-control');
    }

    // =========================================================================
    // END ACCESS CONTROL
    // =========================================================================
}
