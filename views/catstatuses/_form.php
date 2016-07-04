<?php
	
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;
	use yii\helpers\ArrayHelper;
	
	/* @var $this yii\web\View */
	/* @var $model app\modules\admin\models\CatStatuses */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="cat-statuses-form">
	
	<?php $form = ActiveForm::begin(); ?>
	<div class="row">
		<div class="col-md-6">
			<?=$form->field($model, 'name')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-md-6">
			<?=$form->field($model, 'alias')->textInput(['maxlength' => true])?>
		</div>
	</div>
	
	
	<?
		// получаем фабрики
		$status_types = \app\modules\admin\models\StatusType::find()->all();
		// формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
		$status_types_items = ArrayHelper::map($status_types, 'id', 'name');
	?>
	<?=$form->field($model, 'type_id')->dropDownList($status_types_items, ['prompt' => 'Выберите тип статуса'])?>
	
	
	
	<?=$form->field($model, 'sort')->textInput()?>
	
	<?
		// получаем пресеты
		$status_presets = \app\modules\admin\models\StatusPreset::find()->where(['model_name' => 'Cats'])->all();
		// формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
		$status_presets_items = ArrayHelper::map($status_presets, 'id', 'name');
	?>
	
	<? if ($model->type->has_preset) {
		echo $form->field($model, 'preset_id')->dropDownList($status_presets_items, ['prompt' => 'Выберите список значений']);
		$has_preset_js = <<<JS
		var has_preset = true;
JS;
		
	}
	else {
		$has_preset_js = <<<JS
		var has_preset = false;
JS;
		
	};
	?>
	<div class="form-group">
		<?=Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
	</div>
	
	<?php ActiveForm::end(); ?>

</div>
