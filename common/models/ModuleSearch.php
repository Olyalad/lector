<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Module;

/**
 * ModuleSearch represents the model behind the search form about `app\models\Module`.
 */
class ModuleSearch extends Module
{
    public $creator;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['id'], 'integer'],
            [['name', 'creator', 'status'], 'safe'],
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
     * @param bool $hideEmptyModules
     *
     * @return ActiveDataProvider
     */
    public function search($params, $hideEmptyModules = false, $onlyActive = false)
    {
        $query = Module::find();

        // add conditions that should always apply here
        if ($onlyActive)
            $query->active();
        $query->allow();

        if ($hideEmptyModules)
            $query->hideEmptyModules();

        $query->joinWith(['creator']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'name',
                'creator' => [
                    'asc' => ['user.surname' => SORT_ASC, 'user.firstname' => SORT_ASC, 'user.secname' => SORT_ASC],
                    'desc' => ['user.surname' => SORT_DESC, 'user.firstname' => SORT_DESC, 'user.secname' => SORT_DESC],
                ],
                'status',
                'sort_order'
            ],
            'defaultOrder' => ['sort_order' => SORT_ASC]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'module.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'CONCAT(user.surname, " ", user.firstname, " ", user.secname)', $this->creator]);

        return $dataProvider;
    }
}
