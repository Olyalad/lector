<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ModuleExecution;

/**
 * ModuleExecutionSearch represents the model behind the search form about `common\models\ModuleExecution`.
 */
class ModuleExecutionSearch extends ModuleExecution
{
    public $user;
    public $user_group;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user', 'user_group', 'finish'], 'safe'],
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
    public function search($params, $module_id = false)
    {
        $query = ModuleExecution::find();

        if ($module_id)
            $query->andWhere(['module_id' => $module_id]);

        $query->finished();

        // add conditions that should always apply here

        $query->joinWith(['user']);
        $query->joinWith(['user.group']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'user' => [
                    'asc' => ['user.surname' => SORT_ASC, 'user.firstname' => SORT_ASC, 'user.secname' => SORT_ASC],
                    'desc' => ['user.surname' => SORT_DESC, 'user.firstname' => SORT_DESC, 'user.secname' => SORT_DESC],
                ],
                'user_group' => [
                    'asc' => ['groups.name' => SORT_ASC],
                    'desc' => ['groups.name' => SORT_DESC],
                ],
                'finish',
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query
            ->andFilterWhere(['like', 'CONCAT(user.surname, " ", user.firstname, " ", user.secname)', $this->user])
            ->andFilterWhere(['like', 'groups.name', $this->user_group])
            ->andFilterWhere(['like', 'FROM_UNIXTIME(' . $this->tableName() . '.finish, "%d.%m.%Y %H:$i:$s")', $this->finish]);

        return $dataProvider;
    }
}
