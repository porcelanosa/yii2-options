<?php
	
	namespace porcelanosa\yii2options\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use porcelanosa\yii2options\models\OptionPresets;

/**
 * OptionPresetsSearch represents the model behind the search form about `app\modules\admin\models\OptionPresets`.
 */
class OptionPresetsSearch extends OptionPresets
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'defaultPresetValue_id', 'sort', 'active'], 'integer'],
            [['name', 'short_name'], 'safe'],
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
        $query = OptionPresets::find();

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
            'defaultPresetValue_id' => $this->defaultPresetValue_id,
            'sort' => $this->sort,
            'active' => $this->active,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'short_name', $this->short_name]);

        return $dataProvider;
    }
}
