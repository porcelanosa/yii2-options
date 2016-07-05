<?php

	use yii\helpers\Html;
	use yii\widgets\ActiveForm;
	use \app\components\helpers\MyHelper;
	use \yii\helpers\ArrayHelper;

	/* @var $this yii\web\View */
	/* @var $model porcelanosa\yii2options\models\OptionsList */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="options-list-form">

	<?php $form = ActiveForm::begin(); ?>

	<?
		$model_array = [];
		$path        = Yii::getAlias('@app') . '/modules/admin/models/*.php';
		//print_r(glob($path));
		foreach (glob($path) as $filename) {
			$pattern    = '/.+\//i';
			$model_name =
				'app\modules\admin\models\\' . preg_replace($pattern, '', str_replace(".php", "", $filename));
			/**
			 * @var $m \app\modules\admin\models\Cats
			 */
			$m = new $model_name();
			/* имя модели без namespace */
			$clearModelName = MyHelper::modelFromNamespace($model_name);
			if (isset($m->modelFrontName)) {
				$model_array[ $clearModelName ] = $m->modelFrontName;
			}
			if (isset($m->childModels) AND is_array($m->childModels)) {

				foreach (MyHelper::complexModel($model_name) as $chModel=>$chModelName) {
					$model_array[ $chModel ] = $chModelName;
				}
			}
		} ?>
	<div class="row">
		<div class="col-md-7">
			<div class="row">
				<div class="col-md-6">
					<?=$form->field($model, 'name')->textInput(['maxlength' => true])?>
				</div>
				<div class="col-md-6">

					<?
						// получаем Типы параметров
						$option_types = \porcelanosa\yii2options\models\OptionTypes::find()->all();
						// формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
						$option_types_items = ArrayHelper::map($option_types, 'id', 'name');
					?>
					<?=$form->field($model, 'type_id')->dropDownList($option_types_items, ['prompt' => 'Выберите тип поля'])?>
					<!--<hr class="clearfix">-->
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<?=$form->field($model, 'alias')->textInput(['maxlength' => true])?>
					<?=$form->field($model, 'model')->dropDownList($model_array, ['prompt' => 'Выберите модель'])?>
				</div>
				<div class="col-md-6">
					<div class="row">
						<div class="col-md-2">
							<?=$form->field($model, 'minLenght')->textInput()?>
						</div>
						<div class="col-md-2">
							<?=$form->field($model, 'maxLenght')->textInput()?>
						</div>
						<div class="col-md-8">
							<?
								use \kartik\switchinput\SwitchInput;

								echo $form->field($model, 'is_required')->widget(
									SwitchInput::className(),
									[
										'type'          => SwitchInput::CHECKBOX,
										'inlineLabel'   => true,
										'pluginOptions' => [
											'size'      => 'large',
											'labelText' => '<i class="glyphicon glyphicon-list"></i>',
											'onText'    => Yii::t('app', 'REQUIRED'),
											'offText'   => Yii::t('app', 'NOT_REQUIRED'),
											/*'onColor' => 'aqua',
											'offColor' => 'grey',*/
										],
									]
								);
							?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<?
								echo $form->field($model, 'in_filter')->widget(
									SwitchInput::className(),
									[
										'type'          => SwitchInput::CHECKBOX,
										'inlineLabel'   => true,
										'pluginOptions' => [
											'size'      => 'large',
											'labelText' => '<i class="glyphicon glyphicon-list"></i>',
											'onText'    => Yii::t('app', 'IN_FILTER'),
											'offText'   => Yii::t('app', 'NOT_IN_FILTER'),
											/*'onColor' => 'aqua',
											'offColor' => 'grey',*/
										],
									]
								);
							?>

						</div>
					</div>

				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<?

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
				<div class="col-md-4">
					<?=$form->field($model, 'sort')->textInput(['size' => 5])?>

				</div>
			</div>
		</div>
		<div class="col-md-5">
			<? if (!$model->isNewRecord && in_array($model->type->alias, ArrayHelper::merge(MyHelper::TYPES_WITH_PRESET_ARRAY, MyHelper::TYPES_WITH_MULTIPLE_PRESET_ARRAY))): ?>
				<div class="col-md-12" id="presets" data-presetid="<?=$model->preset->id?>">
					<div id="presets-list">
						<div id="presets">
							<div v-for="preset in presets" transition="staggered" stagger="70"
							     class="preset-block sortable-item" id="sort_{{preset.id}}">
								<div v-show="preset != editedPreset">
									<i @click="editPreset(preset)" class="fa fa-fw fa-edit"></i>&nbsp;
									<span v-text="preset.value"></span>
									&nbsp;<i @click="removePreset(preset)" class="fa fa-fw  fa-minus-circle"></i>
								</div>

								<div v-show="preset == editedPreset">

									<input type="hidden" v-model="preset.id"/>
									<input type="hidden" v-model="preset.preset_id"/>
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label for="value" class="control-label">Значение</label>
												<input type="text" class="form-control" v-model="preset.value"
												       name="value"
												       id="value"/>
											</div>
										</div>


										<div class="col-md-4">
											<a class="btn btn-app" @click="savePreset(preset)">
												<i class="fa fa-save"></i> Сохранить значение
											</a>
										</div>
									</div>

								</div>

							</div>
							<hr>
						</div>
						<div class="box box-warning collapsed-box" id="add-preset-box">
							<div class="box-header with-border">
								<h3 class="box-title">Добавить новое значение</h3>

								<div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i
											class="fa fa-plus"></i>
									</button>
								</div>

							</div>
							<div class="box-body" style="display: none;">
								<input type="hidden" v-model="newPreset.preset_id"/>
								<div class="row">

									<div class="col-md-8">
										<div class="form-group">
											<label for="value" class="control-label">Значение</label>
											<input type="text" class="form-control" v-model="newPreset.value"
											       name="value"
											       id="value"/>
										</div>
									</div>
								</div>

								<div class="col-md-4">
									<a class="btn btn-app" v-on:click="addPreset">
										<i class="fa  fa-plus-circle"></i> Добавить
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			<? endif; ?>
		</div>

	</div>

	<div class="form-group">
		<?=Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-lg btn-primary'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>


<? if (!$model->isNewRecord): ?>
	<?
	/*$this->registerJsFile('@vendor/porcelanosa/yii2-options/assets/js/optionsPreset.js', [
		'depends'  => [
			'yii\web\JqueryAsset',
			'\app\assets\VuejsAsset',
			'\app\assets\VueResourceAsset',
			'\yii\jui\JuiAsset'
		],
		'position' => \yii\web\View::POS_END
	], 'vuejs-preset');

	$this->registerJsFile('@vendor/porcelanosa/yii2-options/assets/js/Sortable.js', [
		'depends'  => [
			'yii\web\JqueryAsset',
			'\app\assets\VuejsAsset',
			'\app\assets\VueResourceAsset',
			'\yii\jui\JuiAsset'
		],
		'position' => \yii\web\View::POS_END
	], 'sortable');*/
	\porcelanosa\yii2options\assets\OptionsAsset::register($this);
	?>
<? endif; ?>
