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
    public $termsType, $termsSlug, $meta, $terms;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent', 'postby', 'postsort'], 'integer'],
            [['title', 'content', 'type', 'status', 'layout', 'postdate', 'modified'], 'safe'],
            [['searchKeyword'], 'string', 'max'=>100],
            [['termsType', 'termsSlug'], 'string', 'max'=>50],
            [['meta', 'terms'], 'safe']
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


    public function clientSearch($params, $pageSize=20){


        $query = Post::publishedPostQuery()->select("post.*")->joinWith(['author', 'postTerms']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['publishdate'=>SORT_DESC]],
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

        $query->andFilterWhere([
            'OR',
            ['like', 'post.tile', $this->title],
            ['like', 'post.content', $this->content]
        ]);

        if(!empty($this->meta)){
            $qmeta = $this->query->select('post_id')->from('post_meta')->groupBy('post_id');
            foreach ($this->meta as $key => $value) {
                $qmeta->andWhere(['metakey'=>$key, 'value'=>$value]);
            }
            $qmeta = $qmeta->createCommand()->rawSql;
            $whereMeta = " post.id IN ({$qmeta}) ";
            $query->andWhere($whereMeta);
        }

        if(!empty($this->terms)){
            foreach ($this->terms as $key => $value) {
                $qterms = $this->query->select('post_id')->from('post_terms')->innerJoin('terms', 'post_terms.terms_id=terms.id')->groupBy('post_id');
                $qterms->andWhere(['terms.type'=>$key, 'terms.terms_slug'=>$value]);
                $qterms = $qterms->createCommand()->rawSql;
                $whereTerms = " post.id IN ({$qterms}) ";
                $query->andWhere($whereTerms);
            }
        }

        return $dataProvider;
    }
}
