<?php
namespace admin\controllers;

use Yii;
use admin\models\Media;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

use yii\filters\AccessControl;

class MediaController extends Controller {


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

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }

    public function actionIndex(){
        // $loadFolder = Yii::$app->request->get('loadfolder');
        // $folderName = Yii::$app->request->get('fname');
        // $filename   = Yii::$app->request->get('filename');
        // $detail     = Yii::$app->request->get('detail');
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $mode = Yii::$app->request->get('mode');
        if(empty($mode)){
            $mode = Yii::$app->request->post('mode');
        }
        switch ($mode) {
            case 'loadfolder':
                    $folderPath = Yii::$app->request->get('folderpath');
                    return $this->loadFolder($folderPath);
                break;
            case 'upload-new-file':
                    return $this->uploadFile();
                break;
            case 'create-folder' :
                    $folderPath = Yii::$app->request->post('folderpath');
                    return $this->createFolder($folderPath);
                break;
            case 'file-detail' :
                    $url = Yii::$app->request->get('path');
                    return $this->fileDetail($url);
                break;
            case 'update-detail' :
                    return $this->updateDetail();
                break;
            case 'delete-file' :
                    $url = Yii::$app->request->post('url');
                    $path = str_replace(Yii::getAlias('@web'), Yii::getAlias('@webroot'), $url);
                    return $this->deleteFile($path);
                break;
            default:
                echo json_encode([
                        'large'  =>['mwidth'=>1200, 'mheight'=>720],
                        'medium' =>['mwidth'=>720, 'mheight'=>480],
                        'small'  =>['mwidth'=>480, 'mheight'=>260],
                        'thumb'  =>['mwidth'=>150, 'mheight'=>150],
                    ]);
                break;
        }
    }

    private function createFolder($folderPath){
        $folderPath = Yii::getAlias("@webroot/{$folderPath}");
        $result = ['status' => false, 'message'=>'Folder not exists.',' path'=>$folderPath];
        try {
            if(!file_exists($folderPath)){
                if(!\yii\helpers\FileHelper::createDirectory($folderPath)){
                    throw new \Exception("The system is not allowed to create new folder.");
                }
                $result['status'] = true;
                $result['message'] = 'The folder has been created.';
            }else{
                throw new \Exception("Folder name already exists.");
            }

        } catch (\Exception $e) {
            $result['message'] = $e->getMessage();
        }
        return $result;
    }

    private function loadFolder($folderName){
        $folderPath = Yii::getAlias("@webroot/{$folderName}");
        $folderUrl  = Yii::getAlias("@web/{$folderName}");
        $result = ['status' => false, 'message'=>'Folder not exists.'];
        try {
            $media          = new Media();
            // $sizedFolder    = $media->loadImageSizes();
            $sizedFolder = Yii::$app->getModule('administrator')->media->imageSizes;
            $hiddenFolder   = ['.', '..'];
            if(!file_exists($folderPath)){
                if(!\yii\helpers\FileHelper::createDirectory($folderPath)){
                    throw new \Exception("Folder not exists and system is not allowed to create new folder.");
                }
            }

            foreach ($sizedFolder as $key => $wh) {
                $hiddenFolder[] = $key;
                if(!file_exists($folderPath .  '/' . $key)){
                    \yii\helpers\FileHelper::createDirectory($folderPath .  '/' . $key);
                }
            }

            $files   = [];
            $folders = [];
            // cari folder dalam folder folder;
            $_folders = scandir($folderPath);
            $mtypef  = finfo_open(FILEINFO_MIME_TYPE);
            foreach($_folders as $folder){
                if(!in_array($folder, $hiddenFolder)){
                    $fpath = $folderPath . "/{$folder}";
                    $mimeType = finfo_file($mtypef, $fpath);
                    if($mimeType == 'directory'){
                        $folders[] = [
                            'name' => $folder,
                            'path' => $folderName . "/{$folder}"
                        ];
                    }
                }
            }


            // default load thumb;
            $folderPath = $folderPath . '/small';
            $folderUrl .= '/small';
            $_files = scandir($folderPath);
            $mtype  = finfo_open(FILEINFO_MIME_TYPE);
            $i = 0;
            foreach($_files as $file){
                if(!in_array($file, ['.','..'])){
                    $filepath = $folderPath . "/{$file}";
                    $mimeType = finfo_file($mtype, $filepath);

                    $imgUrl   = $folderUrl . "/{$file}";
                    $files[$i] = ['name'=>$file, 'type'=>$mimeType, 'path'=>$filepath, 'url'=>$imgUrl];
                    if(in_array($mimeType, ['image/png', 'image/jpeg', 'image/gif']) ){
                        $files[$i]['type'] = 'image';
                    }

                    if($mimeType == 'directory'){
                        $files[$i]['icon_url'] = '/icons/folder.png';
                    }
                    else if($mimeType == 'application/pdf'){
                        $files[$i]['type'] = 'pdf';
                        $files[$i]['icon_url'] = '/icons/pdf.png';
                    }

                    $i++;
                }
            }
            $result = ['status'=>true, 'data'=>['files'=>$files, 'currentfolder'=>$folderName, 'folders'=>$folders]];

        } catch (\Exception $e) {
            $result = ['status'=>false, 'message' => $e->getMessage()];
        }

        return $result;

    }

    public function uploadFile(){
        $model = new Media();
        if(Yii::$app->request->isPost){
            $model->sourceFiles = UploadedFile::getInstances($model, 'sourceFiles');
            if ($model->load(Yii::$app->request->post()) && $model->upload()) {
                // file is uploaded successfully
                return ['status'=>true, 'data'=>[], 'message'=>'INI KEUPLOAD'];
            }else{
                return ['status'=>false, 'data'=>[], 'message'=>$model->firstErrors];
            }
        }
    }

    private function fileDetail($url){
        $url = str_replace('small/', '', $url);
        if(($model = Media::find()->where(['fileurl'=>$url])->asArray()->one()) !== null){
            return ['status' => true, 'data'=>$model, 'message'=>''];
        }else{
            return ['status' => false, 'message'=>'File does not have detail information.', 'url'=>$url];
        }
    }

    private function updateDetail(){
        $id = Yii::$app->request->post('ref');
        if(empty($id)){
            $model = new Media();
        }else if(($model = Media::findOne($id))==null){
            $model = new Media();
        }

        if($model->load(Yii::$app->request->post()) && $model->save()){
            return ['status'=>true, 'data'=>[], 'message'=>'File information has been saved.'];
        }else {
            return ['status'=>false, 'data'=>[], 'message'=>$model->firstErrors];
        }
    }

    private function deleteFile($path){
        try {
            $folder = dirname($path);
            $_split = explode('/', $path);
            $filename = end($_split);
            $deleted = [];
            if(file_exists($path)) { unlink($path); $deleted[]=$path;}

            $media          = new Media();
            $sizedFolder    = $media->loadImageSizes();
            foreach ($sizedFolder as $key => $wh) {
                $path_x = "{$folder}/{$key}/{$filename}";
                if(file_exists($path_x)) { unlink($path_x); $deleted[]=$path_x;}
            }
            return ['status'=>true, 'data'=>['deleted'=>$deleted], 'message'=>'File has been deleted.'];
        } catch (\Exception $e) {
            return ['status'=>false, 'data'=>[], 'message'=>$e->getMessage() . ' Line ' . $e->getLine(), ];
        }
        // return ['dirname'=>dirname($path), 'path'=>$path];
    }
}
