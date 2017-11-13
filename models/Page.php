<?php

namespace admin\models;

use Yii;
use admin\db\PostQuery;
use yii\db\Query;
use admin\behaviors\PostBehavior;
/**
 * This is the model class for table "post".
 *
 * @property integer $id
 * @property integer $parent
 * @property string $title
 * @property string $content
 * @property string $type
 * @property string $status
 * @property string $layout
 * @property string $postdate
 * @property integer $postby
 * @property string $modified
 *
 * @property User $postby0
 */
class Page extends \yii\db\ActiveRecord
{

    public $seo_title, $seo_keyword, $seo_description, $header_img;
    public $bulk_id, $bulk_action;
    public $custom_metas;

    public function init(){
        parent::init();
        $this->custom_metas = [];
        if(isset(Yii::$app->params['page_metas'])){
            foreach (Yii::$app->params['page_metas'] as $metaGroup) {
                $input_meta = isset($metaGroup['meta_input']) ? $metaGroup['meta_input'] : [];
                foreach ($input_meta as $key => $value) {
                    $this->custom_metas[$key] = "";
                }
            }
        }
    }

    public function behaviors(){
        return [
            'postBehavior' => [
                'class'         => PostBehavior::className(),
                'attachedClass' => $this,
                'defaultMetas'  => ['seo_keyword', 'seo_description', 'seo_title', 'header_img'],
                'haveCustomMeta' => true,
            ]
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['postby', 'parent', 'postsort'], 'integer'],
            [['content', 'seo_description', 'slug'], 'string'],
            [['postdate', 'modified'], 'safe'],
            [['title'], 'unique'],
            [['seo_keyword', 'header_img'], 'string', 'max' => 120],
            [['seo_title', 'title'], 'string', 'max'=>70],
            [['type', 'status', 'layout'], 'string', 'max' => 30],
            [['postby'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['postby' => 'id']],
            [['parent'], 'exist', 'skipOnError' => true, 'targetClass' => Page::className(), 'targetAttribute' => ['parent' => 'id']],
            ['parent', 'default', 'value'=>0 ],
            [['title', 'content'], 'required'],
            ['custom_metas' , 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Nama Halaman',
            'content' => 'Konten',
            'type' => 'Type',
            'status' => 'Status',
            'layout' => 'Layout',
            'slug' => 'Slug',
            'postdate' => 'Postdate',
            'postby' => 'Postby',
            'modified' => 'modified',
            'user.email' => 'author'
        ];
    }

    public static function find()
    {
        // use CustomerQuery instead of the default ActiveQuery
        return new PostQuery(get_called_class(), 'page');
    }

    public function beforeValidate(){
        $this->postby   = Yii::$app->user->identity->id;
        return parent::beforeValidate();
    }

    public function beforeSave($insert){
        $this->type     = 'page';
        if($insert){
            $this->postdate = empty($this->postdate) ? Yii::$app->getModule('administrator')->dateTime->serverTime('Y-m-d H:i:s') : $this->postdate;
        }else{
            $this->modified = Yii::$app->getModule('administrator')->dateTime->serverTime('Y-m-d H:i:s');
        }
        $this->parent = !is_null($this->parent) ? $this->parent : '0';

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes){
        $oldSlug = isset($changedAttributes['slug']) ? $changedAttributes['slug'] : '';
        $this->cachePage($oldSlug);
        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'postby']);
    }

    public function getParent()
    {
        return $this->hasOne(Page::className(), ['id' => 'parent']);
    }

    public function getLayoutList(){
        $defaultLayoutPage = [
            'homepage' => 'Home Page',
            'singlepage' => 'Single Page',
            'bloglist' => 'Bloglist',
            'contact' => 'Contact Page',
            'other' => 'Other'
        ];
        // get layout from theme;
        if(isset(Yii::$app->params['available_theme_layout'])){
            $defaultLayoutPage = array_merge($defaultLayoutPage, Yii::$app->params['available_theme_layout']);
        }

        return $defaultLayoutPage;
    }

    public function getStatusList(){
        return [
            'draft' => 'Draft',
            'publish' => 'Publish',
            'trash' => 'Trash'
        ];
    }

    public function getParentList(){
        return Page::find()->select(['id', 'title'])->orderBy(['postsort'=>SORT_ASC])->all();
    }

    public function delete(){
        $this->status = 'trash';
        return $this->update();
    }

    public function bulkDelete($pageIds){
        return Page::updateAll(['status'=>'trash'], ['id'=>$pageIds]);
    }

    public function cachePage($oldSlug){
        $query = new Query();
        $postby = $query->select('firstname, lastname, email')->from('user')->where(['id'=>$this->postby])->one();
        $metas = $query->select('*')->from('post_meta')->where(['post_id'=>$this->id])->all();
        $metas = \yii\helpers\ArrayHelper::map($metas, 'metakey', 'value');
        $page  = [
            'title' => $this->title,
            'content' => $this->content,
            'slug' => $this->slug,
            'type' => $this->type,
            'layout' => $this->layout,
            'postdate' => $this->postdate,
            'postby' => $postby,
            'meta_data' => $metas
        ];

        $cache = Yii::$app->cache;
        if(!empty($oldSlug) && ($oldSlug != $this->slug)){
            $cache->delete($oldSlug);
        }

        $cache->set($this->slug, $page);
    }

    public static function getPage($slug){

        $depedency = new \yii\caching\DbDependency(['sql'=>"SELECT modified FROM ".self::tableName()." WHERE slug='{$slug}' LIMIT 1;"]);
        return Yii::$app->cache->getOrSet($slug, function()use($slug){
            $query = new Query();
            $dataPage = $query->select('post.*, user.firstname, user.lastname, user.email')
                        ->from(self::tableName())
                        ->innerJoin(User::tableName(), self::tableName().'.postby='.User::tableName().'.id')
                        ->where(['post.type'=>'page', 'post.slug'=>$slug, 'post.status'=>'publish']);
            if($dataPage->count()==0){
                throw new \yii\web\NotFoundHttpException("Page not found!");
            }
            $page = $dataPage->one();
            $queryMeta  = new Query();
            $postBy = ['firstname'=>$page['firstname'], 'lastname'=>$page['lastname'], 'email'=>$page['email']];
            $metas  = $queryMeta->select('*')->from('post_meta')->where(['post_id'=>$page['id']])->all();
            $metas  = \yii\helpers\ArrayHelper::map($metas, 'metakey', 'value');

            return $result = [
                'title' => $page['title'],
                'content' => $page['content'],
                'slug' => $page['slug'],
                'type' => $page['type'],
                'layout' => $page['layout'],
                'postdate' => $page['postdate'],
                'postby' => $postBy,
                'meta_data' => $metas
            ];
        }, (3600*24*7), $depedency);

    }

}
