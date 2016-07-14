<?php
	
	namespace porcelanosa\yii2options\controllers;
	
	
	use porcelanosa\yii2options\components\helpers\MyHelper;
	use app\modules\admin\models\Cats;
	use porcelanosa\yii2options\models\ModelsOptionsList;
	use porcelanosa\yii2options\models\Options;
	use porcelanosa\yii2options\models\OptionsList;
	use yii\db\ActiveRecord;
	use yii\helpers\Json;
	use yii\web\Controller;
	use yii\helpers\ArrayHelper;
	use Yii;
	
	class DelimageController extends Controller {
		
		public function actionIndex() {
			$success = false;
			if ( Yii::$app->request->isPost ) {
				$model_id = (int) Yii::$app->request->post('model_id');
				$model_name = Yii::$app->request->post('model_name');
				$option_id = (int)Yii::$app->request->post('option_id');
				//$optionsList = OptionsList::find()->where(['alias'=>$option_name])->one();
				$option = Options::find()->where([
					'model_id'=> $model_id,
					'model'=> $model_name,
					'option_id'=> $option_id,
				])->one();
				
				if(unlink(Yii::getAlias('@webroot').$option->value)){
					if($option->delete()){
						$success = true;
					}
				}
				return Json::encode( [ 'success'=> $success, 'path' => $option->value] );
			}
		}
	}