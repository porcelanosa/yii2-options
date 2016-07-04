<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\OptionTypes */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="option-types-form">

	<?php $form = ActiveForm::begin(); ?>
	<div class="row">
		<div class="col-md-7">
            <div class="row">
                <div class="col-md-4">
                    <?=$form->field( $model, 'name' )->textInput( [ 'maxlength' => true ] )?>
                </div>
                <div class="col-md-4">
                    <?=$form->field( $model, 'alias' )->textInput( [ 'maxlength' => true ] )?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'sort')->textInput() ?>
                </div>
            </div>

			<div class="row">
                <div class="col-md-6">
                    <?
                        use kartik\widgets\SwitchInput;
                        echo $form->field($model, 'active')->widget(
                            SwitchInput::className(),
                            [
                                'type'          => SwitchInput::CHECKBOX,
                                'inlineLabel'   => true,
                                'pluginOptions' => [
                                    'size'      => 'large',
                                    'labelText' => '<i class="glyphicon glyphicon-gift"></i>',
                                    'onText'    => Yii::t('app', 'Активен'),
                                    'offText'   => Yii::t('app', 'Неактивен'),
                                    /*'onColor' => 'aqua',
									'offColor' => 'grey',*/
                                ],
                            ]
                        );
                    ?>
                </div>
				<div class="col-md-6">
                    <h1></h1>
					<div class="form-group">
						<?=Html::submitButton( $model->isNewRecord ? Yii::t( 'app', 'Create' ) : Yii::t( 'app', 'Update' ), [ 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-lg btn-primary' ] )?>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-5">
			
		</div>
	</div>


	<?php ActiveForm::end(); ?>

</div>