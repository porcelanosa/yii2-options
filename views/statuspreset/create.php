<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\StatusPreset */

$this->title = Yii::t('app', 'Create Status Preset');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Status Presets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="status-preset-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
