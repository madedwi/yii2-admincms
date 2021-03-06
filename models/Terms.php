<?php

namespace admin\models;

use Yii;
use admin\db\WimaraAR;
/**
 * This is the model class for table "categories".
 *
 * @property string $id
 * @property string $name
 * @property string $category_slug
 * @property string $category_description
 * @property string $parent
 */
class Terms extends WimaraAR
{

    const SCENARIO_CATEGORY = 'category';
    const SCENARIO_TAGS = 'tags';

    const TYPE_CATEGORY = 'category';
    const TYPE_TAG = 'tag';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'terms';
    }

    public function scenarios(){
        return [
            'category'  => ['terms', 'terms_slug', 'terms_descriptions', 'parent', 'type'],
            'tags'      => ['terms', 'terms_slug', 'terms_descriptions', 'parent', 'type'],
            'default'   => ['terms', 'terms_slug', 'terms_descriptions', 'parent', 'type']
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['terms', 'terms_slug'], 'required'],
            [['terms_description'], 'string'],
            [['parent'], 'integer'],
            ['parent', 'default', 'value'=>0],
            [['terms', 'terms_slug'], 'string', 'max' => 100],
            ['type', 'string', 'max'=>50],
            ['type', 'default', 'value'=>'category'],
            ['terms', 'unique', 'targetClass'=>'admin\models\Terms', 'filter'=>function($query){
                if($this->scenario==self::SCENARIO_TAGS){
                    $query->andWhere(['terms.type'=>self::TYPE_TAG]);
                }else{
                    $query->andWhere(['terms.type'=>self::TYPE_CATEGORY]);
                }

            }]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        if($this->scenario=='tags'){
            return [
                'id' => 'ID',
                'terms' => 'Tags',
                'terms_slug' => 'Slug',
                'terms_description' => 'Description',
                'parent' => 'Parent',
                'parentCategory.terms' => 'Parent'
            ];
        }else{
            return [
                'id' => 'ID',
                'terms' => 'Category',
                'terms_slug' => 'Slug',
                'terms_description' => 'Description',
                'parent' => 'Parent',
                'parentCategory.terms' => 'Parent'
            ];
        }
    }

    public function customAttributes(){
        return ['assignedPageSlug', 'formattedSlug'];
    }

    public static function findCategory(){
        return parent::find()->andWhere(['terms.type'=>self::TYPE_CATEGORY]);
    }

    public static function findTag(){
        return parent::find()->andWhere(['terms.type'=>self::TYPE_TAG]);
    }

    public function getParentCategory(){
        return $this->hasOne(self::className(), ['id'=>'parent'])->from(self::tableName() . ' AS parent_Category');
    }

    public function getTerms($where){
        $query =  self::find();
        if(is_object($where) && ($where instanceof \Closure)){
            $where($query);
        }else{
            $query->where($where);
        }

        return $query->asArray()->all();
    }

    public function getCategorySummary(\Closure $where = NULL){
        $category = $this->getTerms(function($query) use ($where){
            $query->select('terms.*, (SELECT COUNT(id) as postount FROM post_terms INNER JOIN post ON post_terms.post_id=post.id WHERE post_terms.terms_id=terms.id AND post.status=\'publish\') AS post_count');
            $query->where(['type'=>self::TYPE_CATEGORY]);

            if(!is_null($where)){
                $where($query);
            }
        });

        return $category;
    }

    public function bulkDeleteCategory(Array $idCategories){
        $connection = $this->db;
        $deleteCommand = $connection->createCommand()->delete(self::tableName(), ['type'=>self::TYPE_CATEGORY, 'id'=>$idCategories]);
        return $deleteCommand->execute();
    }

    public function getCategoryParent(){
        return self::findCategory()->andWhere(['parent' => 0])->asArray()->all();
    }

    public function getCategoriesParentOnly(){
        return self::findCategory()->andWhere(['terms.parent' => 0]);
    }

    public function getChildsCategory(){
        return $this->hasMany(Terms::className(), ['parent'=>'id'])->from(Terms::tableName() . ' as child');
    }

    public function getPosts(){
        return $this->hasMany(Post::className(), ['id' => 'post_id'])->viaTable('post_terms', ['terms_id'=>'id']);
    }

    public function afterFind(){
        parent::afterFind();
        $query  = $this->query->select('post.slug')->from('post')->innerJoin('post_meta', 'post_meta.post_id=post.id')->where(['post.type'=>'page'])->andWhere(['post_meta.metakey' => 'blog_archives', 'post_meta.value'=>$this->id]);
        $data   = $query->one();
        $this->assignedPageSlug = $data['slug'];

        if($this->type == static::TYPE_TAG){
            $this->formattedSlug = '/archives/t/'. $this->terms_slug;
        }else if($this->type == static::TYPE_CATEGORY){
            $this->formattedSlug = '/archives/c/'. $this->terms_slug;
        }
    }
}
