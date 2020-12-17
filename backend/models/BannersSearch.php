<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Banners;

/**
 * BannersSearch represents the model behind the search form of `backend\models\Banners`.
 */
class BannersSearch extends Banners
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sort', 'type', 'is_random', 'bellow_post', 'created_at', 'updated_at'], 'integer'],
            [['title', 'position', 'href', 'active', 'page', 'domain'], 'safe'],
            [['height', 'width'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Banners::find();

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
            'height' => $this->height,
            'width' => $this->width,
            'sort' => $this->sort,
            'type' => $this->type,
            'is_random' => $this->is_random,
            'bellow_post' => $this->bellow_post,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'position', $this->position])
            ->andFilterWhere(['like', 'href', $this->href])
            ->andFilterWhere(['like', 'active', $this->active])
            ->andFilterWhere(['like', 'page', $this->page])
            ->andFilterWhere(['like', 'domain', $this->domain]);

        return $dataProvider;
    }
}
