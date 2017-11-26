<?php

namespace admin\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\Page;

/**
 * PageSearch represents the model behind the search form about `admin\models\Page`.
 */
class PageSearch extends Page
{

    public $meta, $terms;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'postby'], 'integer'],
            [['title', 'content', 'type', 'status', 'layout', 'postdate', 'modified', 'slug'], 'safe'],
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
    public function search($params)
    {
        $query = Page::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['postsort'=>SORT_ASC]]
        ]);

        $this->load($params);
        $query->andFilterWhere(['!=', 'status', 'trash']);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'postdate' => $this->postdate,
            'postby' => $this->postby,
        ]);
        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'layout', $this->layout])
            ->andFilterWhere(['like', 'modifed', $this->modified]);

        return $dataProvider;
    }

    public function clientSearch($params, $pageSize=false){
        $query = Page::findPublishedPage()->joinWith(['user']);

        $pagination = [
            'pageSize' => $pageSize,
        ];

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['postsort'=>SORT_ASC]],
            'pagination' => !$pageSize ? false : $pagination ,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'slug', $this->slug]);

        if(!is_null($this->meta)){
            $qmeta = $this->query->select('post_id')->from('post_meta')->groupBy('post_id');
            foreach ($this->meta as $key => $value) {
                $qmeta->orWhere(['AND', ['=', 'metakey', $key], ['=', 'value', $value]]);
            }
            $qmeta = $qmeta->createCommand()->rawSql;
            $whereMeta = " post.id IN ({$qmeta}) ";
            $query->andWhere($whereMeta);
        }

        if(!is_null($this->terms)){
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
