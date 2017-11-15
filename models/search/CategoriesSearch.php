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
    // public $parentName;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent'], 'integer'],
            [['terms', 'terms_slug', 'terms_description'], 'safe'],
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
}
