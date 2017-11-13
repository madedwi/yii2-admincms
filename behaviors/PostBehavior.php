<?php
namespace admin\behaviors;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class PostBehavior extends \yii\base\Behavior{

    public $attachedClass;
    public $defaultMetas;
    public $haveCustomMeta;
    private $customMetas;
    private $query;


    public function events(){
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsertPost',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdatetPost',
            ActiveRecord::EVENT_AFTER_FIND => 'afterFindPost',
            ActiveRecord::EVENT_INIT => 'initPost'
        ];
    }

    public function initPost(){
        $this->attachedClass->custom_metas = [];
        $this->customMetas = [];
        if(isset(Yii::$app->params['post_metas'])){
            foreach (Yii::$app->params['post_metas'] as $metaGroup) {
                foreach ($metaGroup['meta_input'] as $key => $value) {
                    $this->attachedClass->custom_metas[$key] = '';
                    $this->customMetas[$key] = $value;
                }
            }
        }

        if(isset(Yii::$app->params['page_metas'])){
            foreach (Yii::$app->params['page_metas'] as $metaGroup) {
                foreach ($metaGroup['meta_input'] as $key => $value) {
                    $this->attachedClass->custom_metas[$key] = '';
                    $this->customMetas[$key] = $value;
                }
            }
        }
    }

    public function afterInsertPost(){
        $this->saveCustomMeta(true);
    }

    public function afterUpdatetPost(){
        $this->saveCustomMeta(false);
    }

    private function saveCustomMeta($insert){
        foreach($this->defaultMetas as $meta){
            $this->insertMeta($meta, $this->attachedClass->$meta);
        }

        if($this->haveCustomMeta){
            $dataMultipleMedia = Yii::$app->request->post('multiple_media');
            foreach ($this->customMetas as $meta => $value) {
                if(array_key_exists($meta, $this->attachedClass->custom_metas)){
                    $this->insertMeta($meta, $this->attachedClass->custom_metas[$meta]);
                }else if($value['format'] == 'multiple_media' && !empty($dataMultipleMedia)){
                    $dataMultipleMedia = json_encode($dataMultipleMedia);
                    $this->insertMeta($meta, $dataMultipleMedia);
                }

            }
        }
    }

    private function insertMeta($key, $value){
        $query = " REPLACE INTO post_meta (post_id, metakey, value) VALUES (:postID, :metakey, :value); ";
        $command = Yii::$app->db->createCommand($query);
        $command->bindValue(":postID", $this->attachedClass->id, \PDO::PARAM_INT);
        $command->bindValue(":metakey", $key, \PDO::PARAM_STR);
        $command->bindValue(":value", $value, \PDO::PARAM_STR);
        return $command->execute();
    }

    public function afterFindPost(){
        $this->loadMeta($this->attachedClass->id, $this->attachedClass);
    }

    public function loadMeta($id, $attachedClass=NULL){
        if(!empty($id)){
            $depedency = new \yii\caching\DbDependency(['sql' => (new Query())->select('modified')->from('post')->where(['slug'=>$attachedClass->slug])->createCommand()->rawSql]);

            $metas = Yii::$app->cache->getOrSet("meta_" . $attachedClass->slug, function()use($attachedClass){
                return (new Query())->select('*')->from('post_meta')->where(['post_id'=>$attachedClass->id])->all();
            }, null, $depedency);

            if(!empty($metas) && !is_null($metas)){
                foreach($metas as $meta){
                    if(in_array($meta['metakey'], $this->defaultMetas)){
                        $attachedClass->$meta['metakey'] = $meta['value'];
                    }else{
                        $attachedClass->custom_metas[$meta['metakey']] = $meta['value'];
                    }
                }
            }
        }
    }

}
