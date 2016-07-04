<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\OptionTypes */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Option Types',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Option Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="option-types-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
