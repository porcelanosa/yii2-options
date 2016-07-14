<?php
	
	namespace porcelanosa\yii2options\controllers;
	
	
	use porcelanosa\yii2options\components\helpers\MyHelper;
	use app\modules\admin\models\Cats;
	use porcelanosa\yii2options\models\ModelsOptionsList;
	use porcelanosa\yii2options\models\OptionsList;
	use yii\db\ActiveRecord;
	use yii\helpers\Json;
	use yii\web\Controller;
	use yii\helpers\ArrayHelper;
	
	class DelimageController extends Controller {

		public function beforeAction($action) {
			if(($action->id == "get-options-by-cat-id")
			   OR
			   ($action->id == "get-all-parent-options")
			   OR
			   ($action->id == "delete-option")
			   OR
			   ($action->id == "update")
			   OR
			   ($action->id == "get-options-for-child")
			) {
				$this->enableCsrfValidation = false;
			}

			return parent::beforeAction($action);
		}
		
		public function actionIndex() {
			
			
			return Json::encode([]);
		}
	}