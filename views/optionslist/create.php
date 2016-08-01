<?php
    
    use yii\helpers\Html;
    
    
    /* @var $this yii\web\View */
    /* @var $appUrl string */
    /* @var $model porcelanosa\yii2options\models\OptionsList */
    
    $this->title                   = Yii::t('app', 'Create Options List');
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Options Lists'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="options-list-create">
    
    <h1><?=Html::encode($this->title)?></h1>
    
    <?=$this->render('_form', [
        'model'  => $model,
        'appUrl' => $appUrl,
    ])?>

</div>
