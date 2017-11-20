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
class Page extends \admin\db\WimaraAR
{
    public $bulk_id, $bulk_action;

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

        $defRules = [
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

        return $this->getBehavior('postBehavior')->rules($defRules);
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
                    'user.email' => 'author',
                    'seo_title' => 'Title Alias',
                    'seo_keyword' => 'Keyword',
                    'description' => 'Content Description'
                ];
    }

    public function customAttributes(){
        return ['seo_title', 'seo_keyword', 'seo_description', 'header_img'];
    }

    public function behaviors(){
        return [
            'postBehavior' => [
                'class' => PostBehavior::className(),
                'metaFromClient' => 'page_custom_meta',
                'contentType' => 'page',
            ]
        ];
    }

    public static function find()
    {
        // use CustomerQuery instead of the default ActiveQuery
        return new PostQuery(get_called_class(), 'page');
    }

    public function beforeValidate(){
        $this->postby   = Yii::$app->user->identity->id;
        $this->parent   = !is_null($this->parent) ? $this->parent : '0';
        return parent::beforeValidate();
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
            'homepage'  => 'Home Page',
            'singlepage' => 'Single Page',
            'bloglist'  => 'Bloglist',
            'contact'   => 'Contact Page',
            'other'     => 'Other'
        ];
        // get layout from theme;
        if(isset(Yii::$app->params['theme_layouts'])){
            $defaultLayoutPage = array_merge($defaultLayoutPage, Yii::$app->params['theme_layout']);
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

    public function findPublishedPage(){
        return self::find()->andWhere(['post.status' => 'publish']);
    }
}
