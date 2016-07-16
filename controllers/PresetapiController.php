<?php
	namespace porcelanosa\yii2options\controllers;
	
	use porcelanosa\yii2options\models\OptionPresets;
	use porcelanosa\yii2options\models\OptionPresetValues;
	use yii\rest\ActiveController;
	use yii\web\ForbiddenHttpException;
	
	class PresetapiController extends ActiveController {
		
		public $modelClass = 'porcelanosa\yii2options\models\OptionPresetValues';
		
		public function beforeAction( $action ) {
			$this->enableCsrfValidation = false;
			
			return parent::beforeAction( $action );
		}
		
		public function accessRules() {
			return array(
				array(
					'allow',
					'actions' => array( 'presets' ),
					'users'   => array( '*' ),
				),
			);
		}
		public function actions() {
			return [
				'presets' => [
					'class'               => 'yii\rest\IndexAction',
					'modelClass'          => $this->modelClass,
					'checkAccess'         => [ $this, 'checkAccess' ],
					'prepareDataProvider' => function ( $action ) {
						$id = \Yii::$app->request->post( 'id' );
						/**
						 * @var $preset OptionPresets
						 */
						$preset = OptionPresets::find()->where( [ 'id' => $id ] )->one();
						
						return ($preset!=null)?$preset->optionPresetValues:[];
						
					}
				],
				'view'    => [
					'class'       => 'yii\rest\ViewAction',
					'modelClass'  => $this->modelClass,
					'checkAccess' => [ $this, 'checkAccess' ],
				],
				'create'  => [
					'class'       => 'yii\rest\CreateAction',
					'modelClass'  => $this->modelClass,
					'checkAccess' => [ $this, 'checkAccess' ],
					'scenario'    => $this->createScenario,
				],
				'update'  => [
					'class'       => 'yii\rest\UpdateAction',
					'modelClass'  => $this->modelClass,
					'checkAccess' => [ $this, 'checkAccess' ],
					'scenario'    => $this->updateScenario,
				],
				'delete'  => [
					'class'       => 'yii\rest\DeleteAction',
					'modelClass'  => $this->modelClass,
					'checkAccess' => [ $this, 'checkAccess' ],
				],
				'options' => [
					'class' => 'yii\rest\OptionsAction',
				],
			];
		}
		
		
		public function actionSort() {
			if ( isset( $_POST['sort'] ) && is_array( $_POST['sort'] ) ) {
				$i = 0;
				foreach ( $_POST['sort'] as $item_id ) {
					$block       = OptionPresetValues::findOne( $item_id );
					$block->sort = $i;
					$block->save();
					$i ++;
				}
			}
			echo json_encode( true );
		}
		
		
		/**
		 * Checks the privilege of the current user.
		 *
		 * This method should be overridden to check whether the current user has the privilege
		 * to run the specified action against the specified data model.
		 * If the user does not have access, a [[ForbiddenHttpException]] should be thrown.
		 *
		 * @param string $action the ID of the action to be executed
		 * @param object $model the model to be accessed. If null, it means no specific model is being accessed.
		 * @param array $params additional parameters
		 * @throws ForbiddenHttpException if the user does not have access
		 */
		public function checkAccess($action, $model = null, $params = [])
		{
			return true;
		}
		
	}