<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\OptionPresets */

$this->title = Yii::t('app', 'Create Option Presets');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Option Presets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="option-presets-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
