<?php
	
	namespace porcelanosa\yii2options\models\search;
	
	use Yii;
	use yii\base\Model;
	use yii\data\ActiveDataProvider;
	use porcelanosa\yii2options\models\OptionsList;
	
	/**
	 * OptionsListSearch represents the model behind the search form about `app\modules\admin\models\OptionsList`.
	 */
	class OptionsListSearch extends OptionsList {
		/**
		 * @inheritdoc
		 */
		public function rules() {
			return [
				[ [ 'id', 'is_required', 'type_id', 'active', 'sort', 'minLenght', 'maxLenght' ], 'integer' ],
				[ [ 'name', 'alias', 'model' ], 'safe' ],
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
			$query = OptionsList::find();
			
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
				'id'          => $this->id,
				'is_required' => $this->is_required,
				'type_id'     => $this->type_id,
				'active'      => $this->active,
				'sort'        => $this->sort,
				'minLenght'   => $this->minLenght,
				'maxLenght'   => $this->maxLenght,
			] );
			
			$query->andFilterWhere( [ 'like', 'name', $this->name ] )
			      ->andFilterWhere( [ 'like', 'alias', $this->alias ] )
			      ->andFilterWhere( [ 'like', 'model', $this->model ] )
			;
			
			return $dataProvider;
		}
	}
