<?php
    
    use yii\helpers\Html;
    //use yii\grid\GridView;
    use yii\widgets\Pjax;
    use porcelanosa\yii2options\components\helpers\MyHelper;
    use porcelanosa\yii2options\models\OptionsList;
    use porcelanosa\yii2options\models\OptionTypes;
    use kartik\grid\GridView;
    
    /* @var $this yii\web\View */
    /* @var $searchModel porcelanosa\yii2options\models\search\OptionsListSearch */
    /* @var $dataProvider yii\data\ActiveDataProvider */
    
    $this->title                   = Yii::t('app', 'Options Lists');
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="options-list-index">
    
    <h1><?=Html::encode($this->title)?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    
    <p>
        <?=Html::a(Yii::t('app', 'Create Options List'), ['create'], ['class' => 'btn btn-success'])?>
    </p>
    <?
        function getModelsNameArray()
        {
            $model_array = [];
            //$path        = Yii::getAlias('@app/modules/admin/models/*.php');
            //$modelNamespace = 'app\modules\admin\models\\';
            $path           = Yii::getAlias(\Yii::$app->getModule('options')->model_path);
            $modelNamespace = Yii::getAlias(\Yii::$app->getModule('options')->modelNamespace);
            $pattern        = '/.+\//i';
            //print_r(glob($path));
            foreach (glob($path) as $filename) {
                $model_name = $modelNamespace . preg_replace($pattern, '', str_replace(".php", "", $filename));
                
                $m = new $model_name();
                /* имя модели без namespace */
                $clearModelName = MyHelper::modelFromNamespace($model_name);
                if (isset($m->modelFrontName)) {
                    $model_array[$clearModelName] = $m->modelFrontName;
                }
                if (isset($m->childModels) AND is_array($m->childModels)) {
                    
                    foreach (MyHelper::complexModel($model_name) as $chModel => $chModelName) {
                        $model_array[$chModel] = $chModelName;
                    }
                }
            }
            return $model_array;
        } ?>
    <?
        /**
         * Получаем массив моделей которые есть в OptionsList
         *
         * @return array
         */
        function getOps()
        {
            $ops     = [];
            $options = OptionsList::find()->all();
            foreach ($options as $option) {
                /*$mn = MyHelper::ADMIN_MODEL_NAMESPACE.$option->model;
                $m = new $mn();*/
                if ($option->model) {
                    $pattern = '/\-/';
                    if ( ! preg_match($pattern, $option->model)) {
                        $ops[$option->model] = MyHelper::getModelFrontName($option->model);
                    } else {
                        $ops[$option->model] = MyHelper::getComplexModelChildName($option->model);
                    }
                }
            }
            
            return $ops;
            //return \yii\helpers\ArrayHelper::map($options, 'model', 'name');
        }
    
    ?>
    <?php Pjax::begin(); ?>
    <?=GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'pjax'         => false,
            'bordered'     => true,
            'striped'      => false,
            'condensed'    => false,
            'responsive'   => true,
            'hover'        => true,
            'export'       => false,
            'layout'       => "{pager}\n{summary}\n{items}\n{summary}\n{pager}",
            'columns'      => [
                
                [
                    'attribute' => 'name'
                ],
                
                [
                    //'header' => 'Тип данных',
                    'attribute' => 'model',
                    'content'   => function ($data) {
                        //  формируем полное имя модели с namspace
                        /*$mn = MyHelper::ADMIN_MODEL_NAMESPACE.$data->model;
                        $m = new $mn();*/
                        if ($data->model) {
                            if ( ! preg_match('/\-/', $data->model)) {
                                return MyHelper::getModelFrontName($data->model);
                            } else {
                                return MyHelper::getComplexModelChildName($data->model);
                            }
                        } else {
                            return Yii::t('app', 'NOT_MODEL');
                        }
                    },
                    //'filter'    => getOps()
                    'filter'    => getModelsNameArray()
                ],
                
                [
                    'header'    => 'Тип данных',
                    'attribute' => 'type_id',
                    'content'   => function ($data) {
                        $type = OptionTypes::find()->where(['id' => $data->type_id])->one();
                        
                        return $type->name;
                    },
                ],
                [
                    'class'           => 'kartik\grid\EditableColumn',
                    'attribute'       => 'sort',
                    'editableOptions' => [
                        'header'    => 'Порядок',
                        'inputType' => \kartik\editable\Editable::INPUT_SPIN,
                        'options'   => ['pluginOptions' => ['min' => 0, 'max' => 5000]],
                        
                        'formOptions' => ['action' => ['/options/optionslist/editsort']],
                    ],
                    'hAlign'          => 'right',
                    'vAlign'          => 'middle',
                    'width'           => '100px',
                    'format'          => ['decimal', 0],
                    //'pageSummary'=>true
                ],
                [
                    'class'          => 'porcelanosa\yii2togglecolumn\ToggleColumn',
                    'attribute'      => 'active',
                    // Uncomment if  you don't want AJAX
                    'enableAjax'     => true,
                    'header'         => 'Показывать',
                    'contentOptions' => ['style' => 'width:120px; text-align: center;'],
                    'headerOptions'  => ['class' => 'kartik-sheet-style'],
                ],
                [
                    'class'          => 'yii\grid\ActionColumn',
                    'header'         => 'Действия',
                    'headerOptions'  => ['width' => '100'],
                    'contentOptions' => ['style' => 'width:100px; text-align: center;'],
                    'template'       => '{update}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{delete}',
                ],
            ],
        ]
    );?>
    <?php Pjax::end(); ?></div>
