<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ModuleExecutionAnswers;

/**
 * ModuleExecutionAnswersSearch represents the model behind the search form about `common\models\ModuleExecutionAnswers`.
 */
class ModuleExecutionAnswersSearch extends ModuleExecutionAnswers
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'exec_id', 'question_id', 'useranswer', 'right', 'time'], 'integer'],
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
    public function search($params, $exec_id = false)
    {
        $query = ModuleExecutionAnswers::find();

        // add conditions that should always apply here
        if ($exec_id)
            $query->andWhere(['exec_id' => $exec_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort = false;

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'exec_id' => $this->exec_id,
            'question_id' => $this->question_id,
            'useranswer' => $this->useranswer,
            'right' => $this->right,
            'time' => $this->time,
        ]);

        return $dataProvider;
    }
}
