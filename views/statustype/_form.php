<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Statustype */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="statustype-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
    <?
        use \kartik\switchinput\SwitchInput;

        echo $form->field($model, 'has_preset')->widget(
            SwitchInput::className(),
            [
                'type' => SwitchInput::CHECKBOX,
                'inlineLabel' => true,
                'pluginOptions' => [
                    'size' => 'large',
                    'labelText' => '<i class="glyphicon glyphicon-list"></i>',
                    'onText' => Yii::t('app', 'Есть набор'),
                    'offText' => Yii::t('app', 'Нет набора'),
                    /*'onColor' => 'aqua',
					'offColor' => 'grey',*/
                ],
            ]
        );
    ?>
    <hr class="clearfix">

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
