<?php
	
	namespace porcelanosa\yii2options\models\search;
	
	use Yii;
	use yii\base\Model;
	use yii\data\ActiveDataProvider;
	use porcelanosa\yii2options\models\OptionTypes;
	
	/**
	 * OptionTypesSearch represents the model behind the search form about `app\modules\admin\models\OptionTypes`.
	 */
	class OptionTypesSearch extends OptionTypes {
		/**
		 * @inheritdoc
		 */
		public function rules() {
			return [
				[ [ 'id', 'sort' ], 'integer' ],
				[ [ 'name', 'alias', 'active' ], 'safe' ],
			];
		}
		
		/**
		 * @inheritdoc
		 */
		public function scenarios() {
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
		public function search( $params ) {
			$query = OptionTypes::find();
			
			// add conditions that should always apply here
			
			$dataProvider = new ActiveDataProvider( [
				'query' => $query,
			] );
			
			$this->load( $params );
			
			if ( ! $this->validate() ) {
				// uncomment the following line if you do not want to return any records when validation fails
				// $query->where('0=1');
				return $dataProvider;
			}
			
			// grid filtering conditions
			$query->andFilterWhere( [
				'id'   => $this->id,
				'sort' => $this->sort,
			] );
			
			$query->andFilterWhere( [ 'like', 'name', $this->name ] )
			      ->andFilterWhere( [ 'like', 'alias', $this->alias ] )
			      ->andFilterWhere( [ 'like', 'active', $this->active ] )
			;
			
			return $dataProvider;
		}
	}
