<?php

namespace admin\controllers;

use Yii;
use admin\models\Terms as Categories;
use admin\models\search\CategoriesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\filters\AccessControl;
/**
 * CategoryController implements the CRUD actions for Categories model.
 */
class CategoryController extends Controller
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
     * Lists all Categories models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CategoriesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Categories model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Categories();
        $model->scenario = Categories::SCENARIO_CATEGORY;
        return $this->form($model);
    }

    /**
     * Updates an existing Categories model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        return $this->form($model);
    }

    private function form($model){
        // $parent = $model->find()->select(['id', 'terms'])->where(['parent'=>0])->asArray()->all();
        $parent = $model->categoryParent;
        $parent = \yii\helpers\ArrayHelper::map($parent, 'id', 'terms');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('form', [
                'parent' => $parent,
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Categories model.
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
     * Finds the Categories model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Categories the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Categories::findCategory()->where(['id'=>$id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionBulk(){
        $action = Yii::$app->request->post('bulk_action');
        $categoryIDs = Yii::$app->request->post('bulk_id');
        $model = new Categories();
        $result = [];
        $errorMessgae = '';
        switch ($action) {
            case 'delete':
                    $result = $model->bulkDeleteCategory($categoryIDs);
                    if($result==false){
                        $result = ['status'=>'danger', 'message'=>'Batch delete failed.', 'icon'=>'fa-trash'];
                    }else{
                        $result = ['status'=>'success', 'message'=>'Categorie(s) deleted', 'icon'=>'fa-trash'];
                    }
                break;

            default:
                $result = ['status'=>'danger', 'message'=>'Invalid batch action', 'icon'=>'fa-exclamation'];
                break;
        }

        Yii::$app->session->setFlash('post-categories', $result);
        $this->redirect(['index']);
    }
}
