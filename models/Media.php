<?php

namespace admin\models;

use Yii;
use yii\db\Query;
use yii\imagine\Image;
use Imagine\Gd;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;

/**
 * This is the model class for table "uploads".
 *
 * @property integer $id
 * @property string $type
 * @property string $filename
 * @property string $alt
 * @property string $title
 * @property string $uploaddate
 * @property string $description
 */
class Media extends \yii\db\ActiveRecord
{
    private static $_instance = NULL;
    public $sourceFiles;
    public $uploadPath;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'uploads';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uploaddate'], 'safe'],
            [['type'], 'string', 'max' => 15],
            [['filename'], 'string', 'max' => 50],
            [['alt', 'title', 'description', 'fileurl'], 'string', 'max' => 255],
            ['uploadPath', 'checkUploadPath'],
            [['sourceFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxFiles' => 4],
        ];
    }




    public static function getInstance(){
        if(Media::$_instance == NULL){
            Media::$_instance = new Media();
        }

        return Media::$_instance;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'filename' => 'Filename',
            'alt' => 'Alt',
            'title' => 'Title',
            'uploaddate' => 'Uploaddate',
            'description' => 'Description',
        ];
    }

    public function checkUploadPath($attribute, $params, $validator){
        try {
            $uploadPath = Yii::getAlias('@webroot/' . $this->$attribute);
            if(!file_exists($uploadPath)){
                if(!\yii\helpers\FileHelper::createDirectory($uploadPath)){
                    throw new \Exception("Directory not exist! System does not have permission to create new directory.");
                }
            }

            $targetSize = $this->loadImageSizes();
            foreach ($targetSize as $key => $wh) {
                if(!file_exists($uploadPath .  '/' . $key)){
                    \yii\helpers\FileHelper::createDirectory($uploadPath .  '/' . $key);
                }
            }

        } catch (\Exception $e) {
            $this->addError($attribute, $e->getMessage());
        }

    }

    public function upload()
    {
        if ($this->validate()) {
            $folderPath = Yii::getAlias('@webroot/' . $this->uploadPath);
            // $targetSize = $this->loadImageSizes();
            $targetSize = Yii::$app->getModule('administrator')->media->imageSizes;
            $imagine = Image::getImagine();


            foreach ($this->sourceFiles as $file) {
                $baseName = \admin\helpers\String::slugify($file->baseName);
                $file->saveAs($folderPath. '/' . $baseName . '.' . $file->extension);
                $newImage = $imagine->open($folderPath. '/' .$baseName . '.' . $file->extension);
                $origWidth  = $newImage->getSize()->getWidth();
                $origHeight = $newImage->getSize()->getHeight();
                $ratio      = $origWidth / $origHeight;
                $type = 'image';
                if(strpos($file->type, 'image/') !== FALSE){

                    foreach ($targetSize as $key => $wh) {
                        $newWidth   = null;
                        $newHeight  = null;
                        if(($origWidth>$origHeight) || ($origWidth == $origHeight)){
                            $newWidth = $wh->mwidth > $origWidth ? $origWidth : $wh->mwidth;
                            $newHeight = $newWidth / $ratio;

                        }else if($origWidth < $origHeight){
                            $newHeight = $wh->mheight > $origHeight ? $origHeight : $wh->mheight;
                            $newWidth  = $newHeight * $ratio;

                        }

                        // echo $newWidth . "---" .$newHeight;
                        if(!empty($newWidth) && !empty($newWidth)){
                            $newImage->resize(new Box($newWidth, $newHeight))
                                    ->save($folderPath. '/' . $key . '/' . $baseName . '.' . $file->extension, ['quality'=>100]);
                        }else{
                            return false;
                        }

                    }

                    $type = 'image';
                }

                $command = $this->db->createCommand();
                $command->insert(self::tableName(), [
                    'type' => $type,
                    'filename' => $baseName . '.' .$file->extension,
                    'fileurl' => Yii::getAlias("@web/{$this->uploadPath}/{$baseName}.{$file->extension}"),
                    'alt' => $baseName,
                    'title' => ucwords($baseName),
                    'uploaddate' => (new \DateTime())->format('Y-m-d H:i:s'),
                    'description' => ''
                ])->execute();
            }
            return true;
        } else {
            return false;
        }
    }

    public function loadImageSizes(){
        $query = new Query();
        $sizes = $query->select('option_value')->from('web_options')->where(['option_key'=>'image_sizes'])->one();
        return json_decode($sizes['option_value']);
    }
}
