<?php
	
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;
	
	/* @var $this yii\web\View */
	/* @var $model app\modules\admin\models\StatusPreset */
	/* @var $form yii\widgets\ActiveForm */
?>
	
	<div class="status-preset-form presets-form" data-presetid="<?=$model->id?>">

		<?php $form = ActiveForm::begin(); ?>
		<?
			$model_array = [];
			$path = Yii::getAlias('@app') . '/modules/admin/models/*.php';
			//print_r(glob($path));
			foreach (glob($path) as $filename) {
				$pattern = '/.+\//i';
				$model_name = preg_replace($pattern, '', str_replace(".php", "", $filename));
				$model_array[$model_name] = $model_name;
			} ?>
		<div class="row">
			<div class="col-md-8">
				<?=$form->field($model, 'model_name')->dropDownList($model_array, ['prompt' => 'Выберите модель'])?>


				<?=$form->field($model, 'name')->textInput(['maxlength' => true])?>

				<?=$form->field($model, 'active')->textInput()?>

				<?/*=$form->field($model, 'sort')->textInput()*/?>

				<div class="form-group">
					<?=Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
				</div>
			</div>

			<div class="col-md-4">
				<div class="col-md-12" id="presets">
					<div id="presets-list">
						<div id="presets">
							<div v-for="preset in presets" transition="staggered" stagger="70" class="preset-block sortable-item"  id="sort_{{preset.id}}">
								<div v-show="preset != editedPreset">
									<i @click="editPreset(preset)" class="fa fa-fw fa-edit"></i>&nbsp;
									<span v-text="preset.value"></span>
									&nbsp;<i @click="removePreset(preset)" class="fa fa-fw  fa-minus-circle"></i>
								</div>

								<div v-show="preset == editedPreset">
									<form>
										<input type="hidden" v-model="preset.id"/>
										<input type="hidden" v-model="preset.preset_id"/>
										<div class="row">
											<div class="col-md-8">
												<div class="form-group">
													<label for="value" class="control-label">Значение</label>
													<input type="text" class="form-control" v-model="preset.value" name="value"
													       id="value"/>
												</div>
											</div>


											<div class="col-md-4">
												<a class="btn btn-app" @click="savePreset(preset)">
													<i class="fa fa-save"></i> Сохранить значение
												</a>
											</div>
										</div>
									</form>
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
								<!-- /.box-tools -->
							</div>
							<!-- /.box-header -->
							<div class="box-body" style="display: none;">

								<form>
									<input type="hidden" v-model="newPreset.preset_id"/>
									<div class="row">

										<div class="col-md-8">
											<div class="form-group">
												<label for="value" class="control-label">Значение</label>
												<input type="text" class="form-control" v-model="newPreset.value" name="value"
												       id="value"/>
											</div>
										</div>
									</div>

									<div class="col-md-4">
										<a class="btn btn-app" @click="addPreset">
											<i class="fa  fa-plus-circle"></i> Добавить
										</a>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<?php ActiveForm::end(); ?>

		<!-- /.box-body -->
	</div>
<?
	$this->registerJsFile('/js/admin/presets.js', [
		'depends' => [
			'yii\web\JqueryAsset',
			'\app\assets\VuejsAsset',
			'\app\assets\VueResourceAsset',
			'\yii\jui\JuiAsset'
		],
		'position' => \yii\web\View::POS_END
	], 'vuejs-preset');
	
	$this->registerJsFile('/js/admin/Sortable.js', [
		'depends' => [
			'yii\web\JqueryAsset',
			'\app\assets\VuejsAsset',
			'\app\assets\VueResourceAsset',
			'\yii\jui\JuiAsset'
		],
		'position' => \yii\web\View::POS_END
	], 'sortable');
?>