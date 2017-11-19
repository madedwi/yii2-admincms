<?php
namespace admin\behaviors;

use Yii;
use admin\db\WimaraAR;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class PostBehavior extends \yii\base\Behavior{

    public $attachedClass;
    public $defaultMetas;
    public $metaFromClient;
    public $customMetas;
    private $customMetasRules;
    private $haveCustomMeta;
    private $query;

    public function events(){
        return [
            WimaraAR::EVENT_AFTER_INSERT => 'afterInsertPost',
            WimaraAR::EVENT_AFTER_UPDATE => 'afterUpdatetPost',
            WimaraAR::EVENT_AFTER_FIND => 'afterFindPost',
            WimaraAR::EVENT_INIT => 'initPost'
        ];
    }

    public function initPost(){
        if (!is_subclass_of($this->attachedClass, WimaraAR::className())) {
            throw new \yii\base\InvalidCallException("PostBehavior should only be attached to administrator\db\WimaraAR child class.");
        }
        $this->customMetasRules = [];
        $this->customMetas = [];

        if(array_key_exists($this->metaFromClient, Yii::$app->params)){
            foreach (Yii::$app->params[$this->metaFromClient] as $metaGroup) {
                foreach ($metaGroup['meta_input'] as $key => $value) {
                    $this->attachedClass->setCustomAttribute($key, ArrayHelper::getValue($value, 'default', ''));
                    $this->customMetas[$key] = $value;
                    array_push($this->customMetasRules, ArrayHelper::getValue($value, 'rules', [$key, 'safe']));
                }
            }
        }

        $this->haveCustomMeta = count($this->customMetas) > 0;
    }

    public function getCustomMetasRules(){
        return $this->customMetasRules;
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
                if($this->attachedClass->hasCustomAttribute($meta)){
                    $this->insertMeta($meta, $this->attachedClass->$meta);
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
                    $attachedClass->$meta['metakey'] = $meta['value'];
                }
            }
        }
    }

}
