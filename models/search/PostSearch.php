<?php

namespace admin\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\Post;

/**
 * PostSearch represents the model behind the search form about `admin\models\Post`.
 */
class PostSearch extends Post
{
    public $searchKeyword;
    public $termsType, $termsSlug;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent', 'postby', 'postsort'], 'integer'],
            [['title', 'content', 'type', 'status', 'layout', 'postdate', 'modified'], 'safe'],
            [['searchKeyword'], 'string', 'max'=>100],
            [['termsType', 'termsSlug'], 'string', 'max'=>50]
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
    public function search($params, $pageSize=20)
    {
        $query = Post::find()->select("post.*");

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['postdate'=>SORT_DESC]],
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);

        $this->load($params);

        $query->andFilterWhere(['!=', 'post.status', 'trash']);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(!empty($this->searchKeyword)){
            $query->joinWith(['author', 'postTerms']);
            $query->andFilterWhere(['like', 'post.status', Post::PUBLISHED]);
            $query->andFilterWhere([
                'OR',
                ['like', 'post.title', $this->searchKeyword],
                ['like', 'post.content', $this->searchKeyword],
                ['like', 'user.firstname', $this->searchKeyword],
                ['like', 'user.lastname', $this->searchKeyword],
                ['like', 'user.email', $this->searchKeyword],
                ['like', 'terms.terms', $this->searchKeyword]
            ]);
        }else if(!empty($this->termsType) && !empty($this->termsSlug)){
            $query->joinWith('postTerms');
            $query->andFilterWhere(['post.status'=>Post::PUBLISHED, 'terms.type'=>$this->termsType, 'terms.terms'=>$this->termsSlug]);
        }else{
            // grid filtering conditions
            $query->andFilterWhere([
                'id' => $this->id,
                'parent' => $this->parent,
                'postdate' => $this->postdate,
                'postby' => $this->postby,
                'modifed' => $this->modified,
                'postsort' => $this->postsort,
            ]);

            $query->andFilterWhere(['like', 'title', $this->title])
                ->andFilterWhere(['like', 'content', $this->content])
                ->andFilterWhere(['like', 'type', $this->type])
                ->andFilterWhere(['like', 'status', $this->status])
                ->andFilterWhere(['like', 'layout', $this->layout]);
        }

        return $dataProvider;
    }
}
