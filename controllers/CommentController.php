<?php

namespace admin\controllers;

use Yii;
use admin\models\Comment;
use admin\models\search\CommentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\filters\AccessControl;
/**
 * CommentController implements the CRUD actions for Comment model.
 */
class CommentController extends Controller
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

    /**
     * Lists all Comment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CommentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Comment model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Comment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Comment();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Comment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Comment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Comment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Comment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Comment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionBulk(){
        $action = Yii::$app->request->post('bulk_action');
        $commentIDs = Yii::$app->request->post('bulk_id');
        $model = new Comment();
        $result = [];
        $errorMessgae = '';
        switch ($action) {
            case 'delete':
                    $result = $model->bulkUpdateStatus($commentIDs, 'trash');
                    if($result==false){
                        $result = ['status'=>'danger', 'message'=>'Batch delete failed.', 'icon'=>'fa-trash'];
                    }else{
                        $result = ['status'=>'success', 'message'=>'Comments deleted', 'icon'=>'fa-trash'];
                    }
                break;
            case 'draft' :
                    $result = $model->bulkUpdateStatus($commentIDs, 'draft');
                    if($result==false){
                        $result = ['status'=>'danger', 'message'=>'Batch update status failed.', 'icon'=>'fa-exclamation'];
                    }else{
                        $result = ['status'=>'success', 'message'=>'Status updated', 'icon'=>'fa-check'];
                    }
                break;
            case 'publish' :
                    $result = $model->bulkUpdateStatus($commentIDs, 'publish');
                    if($result==false){
                        $result = ['status'=>'danger', 'message'=>'Batch update status failed.', 'icon'=>'fa-exclamation'];
                    }else{
                        $result = ['status'=>'success', 'message'=>'Status updated', 'icon'=>'fa-check'];
                    }
                break;
            default:
                $result = ['status'=>'danger', 'message'=>'Invalid batch action', 'icon'=>'fa-exclamation'];
                break;
        }

        Yii::$app->session->setFlash('js_alert', $result);
        $this->redirect(['index']);
    }
}
