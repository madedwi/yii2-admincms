<?php

namespace admin\controllers;

use Yii;
use admin\models\Post;
use admin\models\Terms as Categories;
use admin\models\search\PostSearch;
use admin\models\GeneralOptions;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\filters\AccessControl;
/**
 * PostController implements the CRUD actions for Post model.
 */
class PostController extends Controller
{

    private static $adminModule;

    public function beforeAction($action){
        self::$adminModule = Yii::$app->controller->module;

        return parent::beforeAction($action);
    }
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
     * Lists all Post models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Post model.
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
     * Creates a new Post model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Post();
        return $this->renderForm($model);
    }

    /**
     * Updates an existing Post model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        return $this->renderForm($model);

    }

    private function renderForm($model){
        $selectedCategory   = $model->categoryIDs;
        $tags               = $model->tags;
        if(!is_null($tags)){
            $tags = \yii\helpers\ArrayHelper::getColumn($tags, 'terms');
        }else{
            $tags = [];
        }

        $dataModel          = Yii::$app->request->post('Post');
        $web_options        = new GeneralOptions();

        if ($model->load(Yii::$app->request->post())) {
            if($model->save()){
                Yii::$app->session->setFlash('post', ['status'=>'success', 'message'=>'Halaman telah tersimpan.', 'icon'=>'fa-save']);
                return $this->redirect(['/administrator/content/post/' . $model->id . '/update']);
            }else{
                $error = json_encode($model->getErrors());
                Yii::$app->session->setFlash('post', ['status'=>'warning', 'message'=>'Terjadi kesalahan saat menyimpan halaman.' . $error, 'icon'=>'fa-save']);
            }
        }

        $_categories    = (new Categories())->findCategory()->orderBy(['terms'=>SORT_ASC])->asArray()->all();
        $categories     = \yii\helpers\ArrayHelper::map($_categories, 'id', 'terms');
        if(empty($selectedCategory)){
            $slugCategories = \yii\helpers\ArrayHelper::map($_categories, 'terms_slug', 'terms');
        }else{
            $_slugCategories = (new Categories())->findCategory()->andWhere(['id'=>$selectedCategory])->asArray()->all();
            $slugCategories = \yii\helpers\ArrayHelper::map($_slugCategories, 'terms_slug', 'terms');
        }

        return $this->render('form', [
            'model' => $model,
            'selectedCategoryIDs' => $selectedCategory,
            'selectedTags' => implode(',', $tags),
            'categories' => $categories,
            'slugFormat' => $web_options->post_url_format,
            'slugCategories' => $slugCategories,
        ]);
    }

    /**
     * Deletes an existing Post model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionBulk(){
        $action     = Yii::$app->request->post('bulk_action');
        $pageIds    = Yii::$app->request->post('bulk_id');
        $model      = new Post();
        $result     = [];
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
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Post::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionSlugterms(){
        $termsID = Yii::$app->request->post('termsid');
        $terms = new Categories();
        $_t  = $terms->getTerms(['id'=>$termsID]);
        $_ta = \yii\helpers\ArrayHelper::map($_t, 'terms_slug', 'terms');
        return \yii\helpers\Html::dropDownList('slug-category', null, $_ta, ['class'=>'slug-input hidden', 'id'=>'slug-category']);

    }
}
