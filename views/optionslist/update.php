<?php
    
    use yii\helpers\Html;
    
    /* @var $this yii\web\View */
    /* @var $appUrl string */
    /* @var $model porcelanosa\yii2options\models\OptionsList */
    
    $this->title                   = Yii::t('app', 'Update {modelClass}: ', [
            'modelClass' => 'Options List',
        ]) . $model->name;
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Options Lists'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
    $this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="options-list-update">
    
    <h1><?=Html::encode($this->title)?></h1>
    
    <?=$this->render('_form', [
        'model'  => $model,
        'appUrl' => $appUrl,
    ])?>

</div>
