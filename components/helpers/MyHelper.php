<?php
	namespace porcelanosa\yii2options\components\helpers;

	use yii\db\ActiveRecord;
	use yii\helpers\ArrayHelper;
	use yii\helpers\FileHelper;
	use yii\helpers\Html;
	use Yii;

	class MyHelper {
		const ADMIN_MODEL_NAMESPACE = 'app\modules\admin\models\\';

		const TYPES_WITH_PRESET_ARRAY = ['dropdown', 'radiobuton_list'];
		const TYPES_WITH_MULTIPLE_PRESET_ARRAY = ['checkboxlist-multiple', 'dropdown-multiple'];


		public static function modelFromNamespace($namespace) {
			$pattern = '/.+\\\/i';

			return preg_replace($pattern, '', $namespace);
		}

		/**
		 * @param $namespace
		 *
		 * @return array
		 */
		public static function complexModel($namespace) {
			$childModelsArray = []; // возвращаемый массив
			$parentModelName  = self::modelFromNamespace($namespace); // Родительский класс (имя)
			$parentModel      = new $namespace();
			$childModels      = $parentModel->childModels; // массив дочерних классов
			foreach ($childModels as $chModel => $chModelName) {
				//var_dump($chModel);
				$childModelsArray[ $parentModelName . '-' . $chModel ] = $chModelName;
			}

			return $childModelsArray;
		}


		public static function getModelFrontName($model_name) {
			$mn = self::ADMIN_MODEL_NAMESPACE . $model_name;
			$m  = new $mn();

			return $m->modelFrontName;
		}

		/**
		 * @param $model_name
		 *
		 * @return string
		 */
		public static function getComplexModelChildName($model_name) {
			list($parentModelClearName, $childModelClearName) = explode('-', $model_name);
			$parentModelName  = self::ADMIN_MODEL_NAMESPACE . $parentModelClearName;
			$childModelName   = self::ADMIN_MODEL_NAMESPACE . $childModelClearName;
			$parentModel      = new $parentModelName();
			$childModelsArray = $parentModel->childModels;

			return $childModelsArray[ $childModelClearName ];
		}
		
		/**
		 * @param $model_name
		 *
		 * @return string
		 */
		public static function getComplexModelChildValue($model_name) {
			list($parentModelClearName, $childModelClearName) = explode('-', $model_name);
			$parentModelName  = self::ADMIN_MODEL_NAMESPACE . $parentModelClearName;
			$childModelName   = self::ADMIN_MODEL_NAMESPACE . $childModelClearName;
			$parentModel      = new $parentModelName();
			$childModelsArray = $parentModel->childModels;

			return $childModelsArray[ $childModelClearName ];
		}


		public static function post($name) {
			return Yii::$app->request->post($name = NULL);
		}

		//$output_array = [];

		//$full_model_name = self::ADMIN_MODEL_NAMESPACE.$model_name;

		/**
		 * @var $full_model_name string
		 * @var $parent_id_name string
		 * @var $parent_id integer
		 * @var $i integer
		 *
		 * @return array
		 */
		public static function getTree($full_model_name, $parent_id_name, $parent_id = 0, $i = 0) {
			$output_array = [];
			$i            = $i * 2;
			$roots        = $full_model_name::find()->where([$parent_id_name => $parent_id])->all();
			foreach ($roots as $root) {
				$padding                   = str_pad('', $i, '-');
				$output_array[ $root->id ] = $padding.$root->name;
				$current_level_count       = $full_model_name::find()->where([$parent_id_name => $root->id])->count();

				if ($current_level_count > 0) {
					$output_array = ArrayHelper::merge($output_array, self::getTree($full_model_name, $parent_id_name, $root->id, $i + 1));
				}
			}
			return $output_array;
		}
		
		/**
		 * This is the shortcut to DOCUMENT ROOT
		 * If the parameter is given, it will be returned and prefixed with the $_SERVER['DOCUMENT_ROOT']
		 */
		public static function dc( $url = '' ) {
			static $dc;
			if ( $dc === null ) {
				$dc = $_SERVER['DOCUMENT_ROOT'];
			}
			return $dc . '/' . ltrim( $url, '/' );
		}
		
		public static function IFF($url) {
			if($url != '' AND file_exists(self::dc($url)) AND is_file(self::dc($url))){
				return true;
			}
			else {
				return false;
			}
		}
	}