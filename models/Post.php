<?php

namespace admin\models;

use Yii;
use admin\db\PostQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use admin\behaviors\PostBehavior;
/**
 * This is the model class for table "post".
 *
 * @property string $id
 * @property integer $parent
 * @property string $title
 * @property string $content
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
class Post extends \admin\db\WimaraAR
{

    // public $parent, $seo_keyword, $seo_description, $seo_title, $header_img, $enable_comment;
    public $bulk_id, $bulk_action;
    public $terms;
    // public $custom_metas;

    private $savedTerms;

    private $updateViewFromClient = false;

    const PUBLISHED = 'publish';
    const DRAFT     = 'draft';
    const TRASH     = 'trash';

    public function init(){
        $this->savedTerms = [];
        parent::init();
    }

    public function behaviors(){
        return [
            'postBehavior' => [
                'class'         => PostBehavior::className(),
                'metaFromClient' => 'post_custom_meta',
                'contentType' => 'post',
            ]
        ];
    }

    public function customAttributes(){
        return ['seo_title', 'seo_keyword', 'seo_description', 'header_img', 'enable_comment', 'formattedSlug', 'viewCounter'];
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
        $defRules = [
            [['parent', 'postby', 'postsort', 'enable_comment', 'views'], 'integer'],
            [['title', 'slug'], 'required'],
            [['content', 'type', 'status', 'layout'], 'string'],
            [['postdate', 'modified', 'publishdate'], 'safe'],
            [['title', 'header_img'], 'string', 'max' => 255],
            [['seo_title'], 'string', 'max'=>70],
            [['seo_description', 'seo_keyword'], 'string', 'max'=>120],
            ['enable_comment', 'default', 'value'=>0],
            [['postby'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['postby' => 'id']],
            ['terms', 'safe'],
            ['slug', 'unique'],
            ['formattedSlug', 'string'],
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
            'title' => 'Title',
            'content' => 'Content',
            'status' => 'Status',
            'postdate' => 'Post Date',
            'postby' => 'Author ID',
            'modified' => 'Modified Date',
            'postsort' => 'sort',
            'publishdate' => 'Publish Date',
            'author.email' => 'Author Email',
            'seo_title' => 'Meta Title',
            'seo_keyword' => 'Meta Keyword',
            'seo_description' => 'Meta Description',
            'enable_comment' => 'Display Comment Box',
            'slug' => 'Url'
        ];
    }

    public static function find()
    {
        // use CustomerQuery instead of the default ActiveQuery
        return new PostQuery(get_called_class(), 'post');
    }

    public function afterSave($insert, $changetAttribute){
        if(!$this->updateViewFromClient){
            if(array_key_exists('tag', $this->terms) && !is_array($this->terms['tag'])){
                $this->terms['tag'] = explode(',', $this->terms['tag']);
            }
            $this->insertTerms($this->terms);
        }

        return parent::afterSave($insert, $changetAttribute);
    }

    public function afterFind(){

        parent::afterFind();
        $this->slug = '';
        return true;



    }

    public function beforeSave($insert){
        $this->type     = 'post';

        $this->formattedSlug = 'p/' . strip_tags($this->formattedSlug);
        if($this->status == static::PUBLISHED){
            $this->publishdate = Yii::$app->wimaraDateTime->timeToServerZone($this->publishdate)->format('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }

    public function bulkDelete($pageIds){
        // $query = $this->getDb()->createCommand("UPDATE post SET status='trash' WHERE id IN (:id);");
        $command = $this->db->createCommand();
        $update = $command->update(self::tableName(), ['status' => self::TRASH], ['id' => $pageIds]);
        return $update->execute();
    }

    private function formattedSlug(){
        $postUrlFormat = Yii::$app->params['post_url_format'];

        $slugformat = empty($postUrlFormat) ? '{[slug]}' : $postUrlFormat;
        $expl   = explode('/', $slugformat);
        $_expl  = [];
        $_i     = 0;

        foreach ($expl as $i=>$x) {
            if(strpos($x, '-') !== false){
                $_x = explode('-', $x);
                foreach ($_x as $_xi) {
                    $_expl[$_i] = $_xi;
                    $_i++;
                }
                $_i--;
            }else{
                $_expl[$_i] = $x;
            }
            $_i ++;
        }

        $expl = $_expl;
        $text = [];
        $cx = [];
        $slugSegment = [];
        $currentSegment = 0;

        $slugest = "";

        foreach ($expl as $slugKeyword) {
            if($slugKeyword=="{[category]}"){

            }else if(strtolower($slugKeyword)=="{[publish_year]}"){

            }else if(strtolower($slugKeyword)=="{[publish_month_numeric]}"){

            }else if(strtolower($slugKeyword)=="{[publish_month_name]}"){

            }else if(strtolower($slugKeyword)=="{[slug]}"){

            }else{

            }
        }
    }

    public function getCategoryIDs($id = NULL){
        if(is_null($id) && !is_null($this->id)){
            $id = $this->id;
        }
        $query = new Query();
        $query->select('terms_id')
            ->from('post_terms')
            ->innerJoin('terms', 'post_terms.terms_id=terms.id')
            ->where(['post_id'=>$id, 'terms.type'=>Terms::TYPE_CATEGORY]);
        if($query->count()>0){
            $rslt = $query->all();
            $this->savedTerms[Terms::TYPE_CATEGORY] = ArrayHelper::getColumn($rslt, 'terms_id');
        }else{
            $this->savedTerms[Terms::TYPE_CATEGORY] = [];
        }
        return $this->savedTerms[Terms::TYPE_CATEGORY];
    }

    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'postby']);
    }

    public function getComments(){
        return $this->hasMany(Comment::className(), ['parent'=>'id']);
    }

    public function getCommentCount(){
        return Comment::countPostComment($this->id);
    }

    public function getFormattedPublishDate(){
        return Yii::$app->wimaraDateTime->timeFromServerZone($this->publishdate)->format(Yii::$app->params['date_format']);
    }

    public function getPostTerms(){
        return $this->hasMany(Terms::className(), ['id'=>'terms_id'])->viaTable('post_terms', ['post_id'=>'id']);
    }

    public function getPostCategories(){
        return $this->hasMany(Terms::className(), ['id'=>'terms_id'])->viaTable('post_terms', ['post_id'=>'id'])
                ->andWhere(['terms.type'=>Terms::TYPE_CATEGORY]);
    }

    public function getPostTags(){
        return $this->hasMany(Terms::className(), ['id'=>'terms_id'])->viaTable('post_terms', ['post_id'=>'id'])
                ->andWhere(['terms.type'=>Terms::TYPE_TAG]);
    }

    public static function publishedPostQuery(){
        return self::find()->andWhere(['post.status'=>self::PUBLISHED])->orderBy(['post.publishdate' =>SORT_DESC]);
    }

    public function getPostByTerms($type, $slug){
        return self::publishedPostQuery()->joinWith(['postTerms', 'author'])->andWhere(['terms.type'=>$type, 'terms.terms_slug'=>$slug])->all();
    }

    public static function getPostByMeta($key, $value=null){
        $query = self::find()->innerJoin('post_meta', 'post_meta.post_id=post.id');
        // $query->select('post.*')->from('post_meta')->innerJoin('post', 'post_meta.post_id=post.id');
        if(is_array($key)){
            foreach ($key as $mk => $mv) {
                $query->andWhere(['post_meta.metakey'=>$mk, 'post_meta.value'=>$mv]);
            }
        }else if(is_object($key) && ($key instanceof \Closure)){
            $key($query);
        }else if(is_string($key) && !empty($value)){
            $query->where(['post_meta.metakey'=>$key, 'post_meta.value'=>$value]);
        }

        $data = $query->all();
        return $data;
    }

    public function getStatusList(){
        return [
            'draft' => 'Draft',
            'publish' => 'Publish',
            'trash' => 'Trash'
        ];
    }

    public function getTags($id = NULL){
        if(is_null($id) && !is_null($this->id)){
            $id = $this->id;
        }
        $query = new Query();
        $query->select(['terms.terms', 'terms.terms_slug', 'terms.id'])
            ->from('post_terms')
            ->innerJoin('terms', 'post_terms.terms_id=terms.id')
            ->where(['post_id' => $id, 'terms.type'=>Terms::TYPE_TAG]);
        if($query->count()>0){
            $this->savedTerms[Terms::TYPE_TAG] = $query->all();
        }else{
            $this->savedTerms[Terms::TYPE_TAG] = null;
        }
        return $this->savedTerms[Terms::TYPE_TAG];
    }

    private function insertTerms(Array $terms){
        $command    = $this->db->createCommand();
        $modified   = Yii::$app->getModule('administrator')->dateTime->timeToServerZone($this->publishdate)->format('Y-m-d H:i:s');
        $savedTag = [];
        $settedTag = count($this->savedTerms[Terms::TYPE_TAG])>0 ? \yii\helpers\ArrayHelper::getColumn($this->savedTerms[Terms::TYPE_TAG], 'terms') : [];
        if(isset($terms['tag']) || isset($this->savedTerms[Terms::TYPE_TAG])){
            $_newTags       = [];
            $_settedTags    = [];
            if(isset($terms['tag'])){
                $_newTags = $terms['tag'];
            }

            if(isset($this->savedTerms[Terms::TYPE_TAG])){
                $_settedTags = $this->savedTerms[Terms::TYPE_TAG];
            }
            $_tags = array_merge($_newTags, $_settedTags);
            $savedTag = Terms::findTag()->andWhere(['terms'=>$_tags])->asArray()->all();
            $savedTag = ArrayHelper::map($savedTag, 'id', 'terms');
        }

        foreach ($terms as $type=>$data) {
            if(is_array($data) && count($data) > 0){
                foreach ($data as $value) {
                    if($type == Terms::TYPE_CATEGORY){
                        if(($key = array_search($value, $this->savedTerms[Terms::TYPE_CATEGORY])) === false){
                            $command->insert('post_terms', [ 'post_id' => $this->id, 'terms_id' => $value, 'modified'=>$modified])->execute();
                            unset($this->savedTerms[Terms::TYPE_CATEGORY][$key]);
                        }else if(($key = array_search($value, $this->savedTerms[Terms::TYPE_CATEGORY])) !== false){
                            unset($this->savedTerms[Terms::TYPE_CATEGORY][$key]);
                        }
                    }else if($type == Terms::TYPE_TAG){
                        if(($terms_id = array_search($value, $savedTag))!== FALSE){
                            if(($key = array_search($value, $settedTag)) === false){
                                $command->insert('post_terms', [ 'post_id' => $this->id, 'terms_id' => $terms_id, 'modified'=>$modified])->execute();
                                unset($this->savedTerms[Terms::TYPE_TAG][$key]);
                            }else if(($key = array_search($value, $settedTag)) !== false){
                                unset($this->savedTerms[Terms::TYPE_TAG][$key]);
                            }
                        }else if(!empty($value) && strlen($value)>0){
                            $value = trim($value);
                            $newTerms = new Terms();
                            $newTerms->scenario   = Terms::SCENARIO_TAGS;
                            $newTerms->terms      = $value;
                            $newTerms->terms_slug = \admin\helpers\String::slugify($value);
                            $newTerms->type       = Terms::TYPE_TAG;
                            $newTerms->save();
                            $command->insert('post_terms', [ 'post_id' => $this->id, 'terms_id' => $newTerms->id, 'modified'=>$modified])->execute();
                        }
                    }
                }
            }
        }

        // remove saved terms;
        foreach ($this->savedTerms as $type => $data) {
            if(is_array($data) && count($data) > 0){
                foreach ($data as $value) {
                    if($type == Terms::TYPE_CATEGORY){
                        $command->delete('post_terms', ['post_id'=>$this->id, 'terms_id'=>$value])->execute();
                    }else if($type == Terms::TYPE_TAG){
                        if(($terms_id = array_search($value, $savedTag))!== FALSE){
                            $command->delete('post_terms', ['post_id'=>$this->id, 'terms_id'=>$terms_id])->execute();
                        }
                    }
                }
            }
        }
    }

    public function updateViews($pageID=NULL){
        $session = Yii::$app->session;
        if(!$session->has($this->slug ."_views")){
            if(isset($this->id) && !is_null($this->id) && is_null($pageID)){
                $this->updateViewFromClient = true;
                $this->views += 1;
                $this->update();
            }else{
                $command = $this->db->createCommand("UPDATE post SET views=(views+1) WHERE id=:id");
                $command->bindParam(':id', $pageID);
                $command->execute();
            }
            $session->set($this->slug ."_views", $this->views);
        }
    }

    public function toArray($fields = [], $expands = [], $recursive = true){
        $array = parent::toArray($fields, $expands, $recursive);
        $array['formattedPublishDate'] = $this->formattedPublishDate;
        $array['commentCount'] = $this->commentCount;

        if(in_array('postTerms', $expands) && array_key_exists('postTerms', $array)){
            $map = \yii\helpers\ArrayHelper::index($array['postTerms'], 'id', NULL, 'parent');
            $array['postTerms'] = $map;
        }

        return $array;
    }
}
