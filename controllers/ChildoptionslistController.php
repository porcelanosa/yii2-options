<?
	namespace porcelanosa\yii2options\controllers;

	use app\modules\admin\models\Cats;
	use porcelanosa\yii2options\models\OptionsList;
	use Yii;
	use yii\web\Controller;
	use yii\web\NotFoundHttpException;
	use yii\filters\VerbFilter;
	use yii\helpers\ArrayHelper;

	use porcelanosa\yii2options\models\ModelsOptionsList;

	/**
	 * BrandsController implements the CRUD actions for Brands model.
	 */
	class ChildoptionslistController
		extends Controller {

		public function beforeAction($action) {
			$this->enableCsrfValidation = ($action->id !== "update");

			return parent::beforeAction($action);
		}

		/**
		 * @inheritdoc
		 */
		public function behaviors() {
			return ArrayHelper::merge(parent::behaviors(), [
				'verbs' => [
					'class'   => VerbFilter::className(),
					'actions' => [
						'delete' => ['post'],
					],
				],
			]);
		}

		public function actionIndex() {
			$cats           = Cats::find()->all();
			$optionsList    = OptionsList::find()->all();
			$db             = Yii::$app->db;
			$sql            =
				$db->createCommand('SELECT model_id, GROUP_CONCAT(DISTINCT option_id) AS option_ids  FROM models_options_list GROUP BY model_id');
			$q              = $sql->queryAll();
			$original_array = []; // изначальный массив для начала работы
			foreach ($q as $arr) {
				$original_array[] = [[$arr['model_id']], explode(',', $arr['option_ids'])];
			}

			foreach ($original_array as $index => $block_arr) {
				if ($index != 0) {
					foreach ($block_arr[1] as $option_id) {
						foreach ($original_array as $sec_indx=>$second_loop) {
							$compare_index = $sec_indx;
							$compare_arr   = $original_array[ $compare_index ][1];
							if (in_array($option_id, $compare_arr)) {
								//array_push($original_array[ $compare_index ][1], $option_id);
								if (!in_array($block_arr[0][0], $original_array[ $compare_index ][0])) {
									array_push($original_array[ $compare_index ][0], $block_arr[0][0]);
								}
								array_slice($original_array[ $index ][1], $option_id);
							}
						}
					}
				}
			}
			$uniqModelsCats_id = ModelsOptionsList::find()->select('model_id')->distinct()->asArray()->all();
			$modelCats         = ModelsOptionsList::find()->all();
			$i                 = 0;
			$return_array      = [];
			$model_id_array    = [];
			/*foreach ($modelCats as $modelCat) {
				$m_id = $modelCat->model_id;
				$o_id = $modelCat->option_id;
				if (count($return_array) == 0) {
					$i                  = $i + 1;
					$return_array[ $i ] = [[$m_id], [$o_id]];
				} else {
					$inModelArray  = $this->in_arrays($m_id, $return_array, 0);
					$inOptionArray = $this->in_arrays($o_id, $return_array, 1);
					$indxM2        = $this->in_arrays_index($m_id, $return_array, 0);
					$indxO2        = $this->in_arrays_index($o_id, $return_array, 1);
					print $indxM2 . '-' . $indxO2 . '___' . $inModelArray . '-' . $inOptionArray . '<br>';

					if ($inModelArray AND !$inOptionArray) {
						array_push($return_array[ $i ][1], $o_id);
					} elseif (!$inModelArray AND $inOptionArray) {
						array_push($return_array[ $i ][0], $m_id);
					}
					elseif (!$inModelArray AND !$inOptionArray) {
						$i                  = $i + 1;
						$return_array[ $i ] = [[$m_id], [$o_id]];
					}
					print_r($return_array);
				}
			}*/

			/*foreach ($uniqModelsCats_id as $model_id) {
				$curent_model_option_id_array = ModelsOptionsList::find()->select('option_id')->where(['model_id'=>$model_id])->distinct()->asArray()->all();
				$model_id_array[] = $model_id;
				$return_array[] = [$model_id_array, $curent_model_option_id_array];
			}*/

			return $this->render('index', [
				'cats'         => $cats,
				'optionsList'  => $optionsList,
				'uniqModelsId' => $uniqModelsCats_id,
				'return_array' => $original_array
			]);
		}

		protected function in_arrays($needle, $arrays, $index) {
			print 'Needle ' . $needle . '<br>';
			foreach ($arrays as $arr) {
				if (in_array($needle, $arr[ $index ])) {
					print $needle;
					print_r($arr[ $index ]);

					return true;
				} else {
					return false;
				}
			}
		}

		protected function in_arrays_index($needle, $arrays, $index) {
			foreach ($arrays as $ind => $arr) {
				if (in_array($needle, $arr[ $index ])) {
					return $ind;
				} else {
					return false;
				}
			}
		}

		public function actionUpdate() {
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			$postBody                    = Yii::$app->getRequest()->getBodyParams();
			$cats                        = $postBody['cats'];
			$options                     = $postBody['options'];
			foreach ($cats as $cat_id) {
				$current_cat = ModelsOptionsList::findOne(['model_id' => $cat_id]);
				foreach ($options as $option_id) {
					$coincidence =
						ModelsOptionsList::find()->where([
							'model_id'  => $cat_id,
							'option_id' => $option_id
						])->count() > 0 ? true : false;
					if (!$coincidence) {
						$newModelOptions             = new ModelsOptionsList();
						$newModelOptions->model_id   = $cat_id;
						$newModelOptions->model_name = 'Cats';
						$newModelOptions->option_id  = $option_id;
						$newModelOptions->save();
					}
				}
			}


		}
	}