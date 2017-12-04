<?php

namespace admin\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\models\Terms;

/**
 * CateoriesSearch represents the model behind the search form about `common\models\Categories`.
 */
class CategoriesSearch extends Terms
{
    public $post_id, $post_slug;
    // public $parentName;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent'], 'integer'],
            [['terms', 'terms_slug', 'terms_description'], 'safe'],
            ['post_id', 'integer'],
            [['post_slug'], 'safe']
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
        $query = Terms::findCategory()->joinWith('parentCategory');

        // add conditions that should always apply here
        $query->andWhere(['terms.type'=>'category']);
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
        ]);

        $query->andFilterWhere(['like', 'terms.terms', $this->terms])
            ->andFilterWhere(['like', 'terms.terms_slug', $this->terms_slug])
            ->andFilterWhere(['like', 'terms.terms_description', $this->terms_description]);

        return $dataProvider;
    }

    public function clientSearch($params, $pagination){
        $query = Terms::getCategoriesParentOnly()->joinWith(['childsCategory']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination ,
        ]);

        $this->load($params);

        if(!$this->validate()){
            return $dataProvider;
        }

        $query->andFilterWhere(['OR', ['like', 'terms.terms', $this->terms], ['like', 'child.terms', $this->terms]])
                ->andFilterWhere(['OR', ['like', 'terms.terms_slug', $this->terms_slug], ['like', 'child.terms_slug', $this->terms_slug]]);


        if(!is_null($this->post_slug)){
            $subQuery = $this->query->select('terms_id')->from('post_terms')->innerJoin('post', ['post_terms.post_id'=>'post.id'])->where(['post.slug'=>$this->post_slug]);
            $subQuery = $subQuery->createCommand()->rawSql;

            $query->andFilterWhere("terms.id IN ({$subQuery}) OR child.id IN ({$subQuery})");
        }

        if(!is_null($this->post_id)){
            $subQuery = $this->query->select('terms_id')->from('post_terms')->where(['post_terms.post_id'=>$this->post_id]);
            $subQuery = $subQuery->createCommand()->rawSql;

            $query->andFilterWhere("terms.id IN ({$subQuery}) OR child.id IN ({$subQuery})");
        }

        return $dataProvider;

    }
}
