<?php

namespace admin\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\Comment;
use yii\helpers\ArrayHelper;

/**
 * CommentSearch represents the model behind the search form about `common\models\Comment`.
 */
class CommentSearch extends Comment
{

    public $parent_title, $parent_type;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent', 'postby', 'postsort'], 'integer'],
            [['title', 'content', 'slug', 'type', 'status', 'layout', 'postdate', 'publishdate', 'modified'], 'safe'],
            [['parent_title', 'parent_type'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Comment::find()
                    ->select(['comment.*', 'parent_title'=>'post.title', 'parent_type'=>'post.type'])
                    ->innerJoin(self::tableName(), 'comment.parent=post.id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'parent' => $this->parent,
            'postdate' => $this->postdate,
            'publishdate' => $this->publishdate,
            'postby' => $this->postby,
            'modified' => $this->modified,
            'postsort' => $this->postsort,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'layout', $this->layout]);


        // if($dataProvider->totalCount > 0){
        //     $models = $dataProvider->models;
        //     // get id comment;
        //     $IDs = ArrayHelper::getColumn($models, 'id');
        //     $query = new \yii\db\Query();
        //     $query->select('*')->from('post_meta')->where(['post_id'=>$IDs]);
        //     $commentMetas = $query->all();
        //     $commentMetas = ArrayHelper::index($commentMetas, NULL, 'post_id');
        //     foreach ($models as $key => $data) {
        //         if(array_key_exists($data->id, $commentMetas)){
        //             $metas = $commentMetas[$data->id];
        //             foreach ($metas as $meta) {
        //                 $models[$key]->$meta['metakey'] = $meta['value'];
        //             }
        //         }
        //     }
        //     $dataProvider->models = $models;
        // }
        return $dataProvider;
    }
}
