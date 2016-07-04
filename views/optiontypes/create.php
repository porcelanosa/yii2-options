<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\OptionTypes */

$this->title = Yii::t('app', 'Create Option Types');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Option Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="option-types-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
