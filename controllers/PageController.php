<?php

namespace admin\controllers;

use Yii;
use admin\models\Page;
use admin\models\Terms as Tags;
use admin\models\search\PageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\filters\AccessControl;

/**
 * PageController implements the CRUD actions for Page model.
 */
class PageController extends Controller
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
                    'delete'=> ['POST'],
                    'bulk' => ['POST']
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
     * Lists all Page models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Page model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Page model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Page();
        return $this->renderForm($model);
    }

    private function renderForm($model){

        if ($model->load(Yii::$app->request->post())) {
            if($model->save()){
                Yii::$app->session->setFlash('post', ['status'=>'success', 'message'=>'Halaman telah tersimpan.', 'icon'=>'fa-save']);
                return $this->redirect(['update', 'id'=>$model->id]);
            }else{
                $error = json_encode($model->getErrors());
                Yii::$app->session->setFlash('post', ['status'=>'warning', 'message'=>'Terjadi kesalahan saat menyimpan halaman.' . $error, 'icon'=>'fa-save']);

            }
        }

        $parent = $model->getParentList();

        $categories = Tags::findCategory()->asArray()->all();
        $tags       = Tags::findTag()->asArray()->all();

        return $this->render('form', [
            'parent' => $parent,
            'model' => $model,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    /**
     * Updates an existing Page model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        return $this->renderForm($model);
    }

    /**
     * Deletes an existing Page model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionBulk(){
        $action = Yii::$app->request->post('bulk_action');
        $pageIds = Yii::$app->request->post('bulk_id');
        $model = new Page();
        $result = [];
        $errorMessgae = '';
        switch ($action) {
            case 'delete':
                    $result = $model->bulkDelete($pageIds);
                    if($result==false){
                        $result = ['status'=>'danger', 'message'=>'Batch delete failed.', 'icon'=>'fa-trash'];
                    }else{
                        $result = ['status'=>'success', 'message'=>'Page deleted', 'icon'=>'fa-trash'];
                    }
                break;

            default:
                $result = ['status'=>'danger', 'message'=>'Invalid batch action', 'icon'=>'fa-exclamation'];
                break;
        }

        Yii::$app->session->setFlash('js_alert', $result);
        $this->redirect(['index']);
    }

    /**
     * Finds the Page model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Page the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Page::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionTags(){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $_categories    = (new Tags())->findTag()->orderBy(['terms'=>SORT_ASC])->asArray()->all();
        $categories     = \yii\helpers\ArrayHelper::getColumn($_categories, 'terms');

        return $categories;
    }

    public function actionClient(){

    }
}
