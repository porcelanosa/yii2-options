<?php
	
	namespace porcelanosa\yii2options\controllers;
	
	
	use porcelanosa\yii2options\models\Options;
	use yii\filters\AccessControl;
	use yii\filters\VerbFilter;
	use yii\helpers\Json;
	use yii\web\Controller;
	use Yii;
	
	/**
	 * Class DelimageController
	 *
	 * @package porcelanosa\yii2options\controllers
	 */
	class DelimageController extends Controller {
		
		public function beforeAction($action) {
			$this->enableCsrfValidation = false;
			return parent::beforeAction($action);
		}
		public function behaviors()
		{
			return [
				'verbs' => [
					'class' => VerbFilter::className(),
					'actions' => [
						'delete' => ['post'],
					],
				],
				'access' => [
					'class' => AccessControl::className(),
					'rules' => [
						[
							'allow' => true,
							'roles' => ['@'],
						],
					],
				],
			];
		}
		public function actionIndex() {
			Yii::$app->controller->enableCsrfValidation = false;
			$success = false;
			if ( Yii::$app->request->isPost ) {
				$model_id = (int) Yii::$app->request->post('model_id');
				$model_name = Yii::$app->request->post('model_name');
				$option_id = (int)Yii::$app->request->post('option_id');
				/**
				 * @var $option Options
				 */
				$option = Options::find()->where([
					'model_id'=> $model_id,
					'model'=> $model_name,
					'option_id'=> $option_id,
				])->one();
				
				if(unlink($_SERVER['DOCUMENT_ROOT'].$option->value)){
					if($option->delete()){
						$success = true;
					}
				}
				return Json::encode( [ 'success'=> $success, 'path' => $option->value] );
			}
		}
	}