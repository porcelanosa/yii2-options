<?php
/**
 * Created by PhpStorm.
 * User: sasha
 * Date: 13.12.2015
 * Time: 21:11
 */

namespace app\modules\admin\controllers;

use app\modules\admin\models\OptionPresets;
use app\modules\admin\models\StatusPresetValues;
use Yii;
use yii\base\Model;
use \yii\base\Action;

use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\web\Controller;

class OptionsapiController extends ActiveController
{

    public $modelClass = 'app\modules\admin\models\OptionPresetValues';

    public function actions()
    {
        return [
            'presets' => [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'prepareDataProvider' => function ($action) {
                    $id = \Yii::$app->request->post('id');
                    $preset = OptionPresets::find()->where(['id' => $id])->one();
                    return $preset->statusPresetValues;

                }
            ],
            'view' => [
                'class' => 'yii\rest\ViewAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'create' => [
                'class' => 'yii\rest\CreateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->createScenario,
            ],
            'update' => [
                'class' => 'yii\rest\UpdateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->updateScenario,
            ],
            'delete' => [
                'class' => 'yii\rest\DeleteAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    public function actionCreatesize() {
        /*if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }*/

        /* @var $model \yii\db\ActiveRecord */
        $model = new Sizes([
            'scenario' => $this->createScenario,
        ]);

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $request = Yii::$app->getRequest();
            $response->setStatusCode(200);
            $id = implode(',', array_values($model->getPrimaryKey(true)));


            $url = parse_url($request->referrer, PHP_URL_PATH);
            $matches = array();
            $reg = preg_match('/(\d+)$/', $url, $matches);
            $work_id = (int) $matches[0];
            $relation = new WorksSizes();
            $relation->work_id = $work_id;
            $relation->size_id = $id;
            $relation->save();
                $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }

    /**
     * @param $id
     * @throws ServerErrorHttpException
     */
    public function actionDeletesize($id)
    {

        $action = new \yii\rest\Action('delete', $this, ['modelClass' => $this->modelClass,'checkAccess' => [$this, 'checkAccess']]);
        //$action->modelClass = 'app\modules\admin\models\Sizes';

        $model = $action->findModel($id);

        if ($action->checkAccess) {
            call_user_func($action->checkAccess, $action->id, $model);
        }

        $request = Yii::$app->getRequest();
        $url = parse_url($request->referrer, PHP_URL_PATH);
        $matches = array();
        $reg = preg_match('/(\d+)$/', $url, $matches);
        $work_id = (int) $matches[0];
        $relation = WorksSizes::find()->where(['size_id'=>$id, 'work_id'=>$work_id])->one();
        $relation->delete();

        if ($model->delete() === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }
        else {

        }

        Yii::$app->getResponse()->setStatusCode(204);
    }




    public function actionSort()
    {
        if (isset($_POST['sort']) && is_array($_POST['sort'])) {
            $i = 0;
            foreach ($_POST['sort'] as $item_id) {
                $block = StatusPresetValues::findOne($item_id);
                $block->sort = $i;
                $block->save();
                $i++;
            }
        }
        echo json_encode(true);
    }

    public function actionArtistworks3()
    {
        $id = \Yii::$app->request->post('id');
        $series = Series::find()->joinWith('works')->where(['artist_id' => $id])->all();
        //return $series;
        return new ActiveDataProvider([
            'query' => Series::find()->joinWith('works')->where(['artist_id' => $id])->all(),
        ]);
    }

}