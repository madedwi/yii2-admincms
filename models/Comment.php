<?php

namespace admin\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "post".
 *
 * @property string $id
 * @property integer $parent
 * @property string $title
 * @property string $content
 * @property string $slug
 * @property string $type
 * @property string $status
 * @property string $layout
 * @property string $postdate
 * @property string $publishdate
 * @property integer $postby
 * @property string $modified
 * @property integer $postsort
 *
 * @property User $postby0
 */
class Comment extends \yii\db\ActiveRecord
{

    public $comment_email, $comment_name, $comment_website;
    public $parent_title, $parent_type;
    public $commentReplys;
    public $commentCount;
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
            [['content', 'parent', 'comment_name', 'comment_email'], 'required'],
            [['parent', 'postby', 'postsort'], 'integer'],
            [['title'], 'default', 'value'=>'comment'],
            [['content', 'status', 'layout'], 'string'],
            [['postdate', 'publishdate', 'modified'], 'safe'],
            [['title', 'slug'], 'string', 'max' => 255],
            [['type'], 'default', 'value'=>'comment'],
            ['comment_email', 'email'],
            [['comment_name', 'comment_website'], 'string', 'max'=>100],
            [['postby'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['postby' => 'id']],
            // ['parent', 'exist', 'targetClass'=>self::className(), 'targetAttribute'=>['parent'=>'id']],
            [['parent_title', 'parent_type', 'replys'], 'safe'],
            [['commentCount'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent' => 'Parent',
            'title' => 'Title',
            'content' => 'Content',
            'slug' => 'Slug',
            'type' => 'Type',
            'status' => 'Status',
            'layout' => 'Layout',
            'postdate' => 'Postdate',
            'publishdate' => 'Publishdate',
            'postby' => 'Postby',
            'modified' => 'Modified',
            'postsort' => 'Postsort',
            'parent_title' => 'Post / Article',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */

     public static function find(){
         return parent::find()->from(self::tableName() . ' as comment')->andWhere(['and', ['comment.type'=>'comment'], ['<>', 'comment.status', 'trash']]);
     }

     public static function findPublished(){
         return self::find()->andWhere(['comment.status'=>'publish']);
     }

     public function bulkUpdateStatus(Array $IDs, $status='draft'){
         return self::updateAll(['status'=>$status], ['id'=>$IDs]);
     }

     public function getPost(){
         return $this->hasOne(Post::className(), ['id'=>'parent']);
     }

     public function getParentComment(){
         return $this->hasOne(Comment::className(), ['id'=>'parent'])->from(self::tableName() . ' as parent')->where(['and', ['parent.type'=>'comment'], ['<>', 'parent.status', 'trash']]);
     }

     public function afterFind(){
         parent::afterFind();
         // load meta data
         foreach ($this->metaDatas as $key => $value) {
             $this->$key = $value;
         }
        //  $this->commentReplys = \yii\helpers\ArrayHelper::toArray($this->replys);
     }

     public function getMetaDatas(){
         $query = new Query();
         $_metas = $query->select('*')->from('post_meta')->where(['post_id'=>$this->id])->all();
         $metas  = \yii\helpers\ArrayHelper::map($_metas, 'metakey', 'value');
         return $metas;
     }

     public function getReplys(){
         return $this->hasMany(Comment::className(), ['parent'=>'id']);
     }

     public function countPostComment($post_id = NULL){
         $cond = NULL;
         if(!is_null($post_id)){
            $cond = ['parent' => $post_id];
         }
         return self::find()->where($cond)->count();
     }

}
