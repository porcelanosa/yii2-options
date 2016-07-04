<?php

namespace porcelanosa\yii2options\controllers;

use app\components\helpers\MyHelper;
use porcelanosa\yii2options\models\OptionPresets;
use porcelanosa\yii2options\models\OptionTypes;
use Yii;
use porcelanosa\yii2options\models\OptionsList;
use porcelanosa\yii2options\models\search\OptionsListSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OptionslistController implements the CRUD actions for OptionsList model.
 */
class OptionslistController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all OptionsList models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OptionsListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OptionsList model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new OptionsList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OptionsList();
        $preset_id = NULL;

        if (Yii::$app->request->isPost) {
            $type_id = Yii::$app->request->post()['OptionsList']['type_id'];
            if ($type_id) {
                /**
                 * @var $option_type OptionTypes
                 * @var $preset      OptionPresets
                 */
                $option_type = OptionTypes::find()->where(['id' => $type_id])->one();
                if ($option_type) {
                    $type_alias = $option_type->alias;
                    if (in_array($type_alias, ArrayHelper::merge(MyHelper::TYPES_WITH_PRESET_ARRAY, MyHelper::TYPES_WITH_MULTIPLE_PRESET_ARRAY))) {
                        $preset = new OptionPresets();
                        $preset->save();
                        $preset_id = $preset->id;
                    }
                }
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if($preset_id) {
                $model->preset_id = $preset_id;
                $model->save();
            }
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing OptionsList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing OptionsList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the OptionsList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OptionsList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OptionsList::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
