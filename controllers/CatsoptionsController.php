<?php
    /**
     * Created by PhpStorm.
     * User: Sasha-PC
     * Date: 02.06.2016
     * Time: 8:33
     */
    
    namespace porcelanosa\yii2options\controllers;
    
    
    use porcelanosa\yii2options\components\helpers\MyHelper;
    use common\models\Cats;
    use porcelanosa\yii2options\models\ModelsOptionsList;
    use porcelanosa\yii2options\models\OptionsList;
    use yii\db\ActiveRecord;
    use yii\web\Controller;
    use yii\helpers\ArrayHelper;
    use Yii;
    use yii\web\Response;
    
    class CatsoptionsController extends Controller
    {
        public function beforeAction($action)
        {
            $this->enableCsrfValidation = false;
            
            return parent::beforeAction($action);
        }
        
        /*public function beforeAction($action) {
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
        }*/
        
        public function actionIndex()
        {
            $modelRelation = 'Cats-Items';
            $model_name    = 'Cats';
            $appUrl        = '/backend/';
            $cats          = Cats::find()->all();
            
            return $this->render(
                'index', [
                    'model_name'  => $model_name,
                    'appUrl'      => $appUrl,
                    'cats'        => $cats,
                    'optionsList' => $this->actionCommonOptionsList($modelRelation), //  child options
                    //'CommonOptionsList' => $this->actionCommonOptionsList($model_name) //  common options for CAts
                ]
            );
        }
        
        public function actionDeleteOption()
        {
            $return = ['success' => false];
            // для предотвращения ошибки Exception 'yii\base\InvalidParamException' with message 'Response content must not be an array.'
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $postBody   = Yii::$app->getRequest()->getBodyParams();
            $model_id   = $postBody['model_id'];
            $option_id  = $postBody['option_id'];
            $model_name = 'Cats';
            $option     = ModelsOptionsList::find()->where(
                [
                    'model_id'   => $model_id,
                    'model_name' => $model_name,
                    'option_id'  => $option_id
                ]
            )->one()
            ;
            if ($option) {
                if ($option->delete()) {
                    $return = ['success' => true];
                };
            };
            
            return $return;
        }
        
        
        public function actionUpdate()
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $postBody                   = Yii::$app->getRequest()->getBodyParams();
            $cat_id                     = $postBody['cat_id'];
            $options                    = $postBody['options'];
            $addedItem                  = [];
            foreach ($options as $option_id) {
                $coincidence =
                    ModelsOptionsList::find()->where(
                        [
                            'model_id'  => $cat_id,
                            'option_id' => $option_id
                        ]
                    )->count() > 0 ? true : false;
                if ( ! $coincidence) {
                    $addedItem                   = ['option_id' => $option_id, 'model_id' => $cat_id];
                    $newModelOptions             = new ModelsOptionsList();
                    $newModelOptions->model_id   = $cat_id;
                    $newModelOptions->model_name = 'Cats';
                    $newModelOptions->option_id  = $option_id;
                    $newModelOptions->save();
                }
            }
            
            return $addedItem;
        }
        
        public function actionCommonOptionsList($model_name)
        {
            $CommonOptionsList = OptionsList::find()->where(['model' => $model_name])->all();
            
            return $CommonOptionsList;
        }
        
        public function actionGetOptionsByCatId()
        {
            // для предотвращения ошибки Exception 'yii\base\InvalidParamException' with message 'Response content must not be an array.'
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $postBody   = Yii::$app->getRequest()->getBodyParams();
            $model_id   = $postBody['model_id'];
            $model_name = 'Cats';
            
            return $this->getChildOptions($model_name, $model_id);
        }
        
        
        public function actionGetAllParentOptions()
        {
            // для предотвращения ошибки Exception 'yii\base\InvalidParamException' with message 'Response content must not be an array.'
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $postBody   = Yii::$app->getRequest()->getBodyParams();
            $model_id   = $postBody['model_id'];
            $model_name = 'Cats';
            
            return $this->getAllParentOptions($model_name, $model_id);
        }
        
        public function actionGetOptionsForChild()
        {
            // для предотвращения ошибки Exception 'yii\base\InvalidParamException' with message 'Response content must not be an array.'
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $postBody   = Yii::$app->getRequest()->getBodyParams();
            $model_name = $postBody['model_name'];
            
            return $this->actionCommonOptionsList($model_name);
        }
        
        protected function getChildOptions($model_name, $cat_id)
        {
            return ModelsOptionsList::find()->where(['model_name' => $model_name, 'model_id' => $cat_id])->all();
        }
        
        
        protected function getAllParentOptions($model_name, $model_id)
        {
            $options_arr = [];
            /** @var $mn ActiveRecord */
            /** @var $cat Cats */
            $mn  = Yii::$app->getModule('options')->modelNamespace . $model_name;
            $cat = $mn::findOne(['id' => $model_id]);
            if ($cat->parent) {
                if ($cat->parent->id != 0) {
                    $options_arr = ArrayHelper::merge($this->getChildOptions($model_name, $cat->parent->id),
                        $this->getAllParentOptions($model_name, $cat->parent->id));
                } /*else {
				$options_arr = ArrayHelper::merge($this->getChildOptions($model_name, $cat_id), $options_arr);
			}*/
            }
            
            return $options_arr;
        }
    }