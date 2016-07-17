<?
	use yii\helpers\Html;
	use porcelanosa\yii2options\models\OptionsList;
	use porcelanosa\yii2options\OptionsWidget;
	use porcelanosa\yii2options\OptionsBehavior;
	use porcelanosa\yii2options\components\helpers\MyHelper;
	
	/**
	 * @var $optionList                 OptionsList
	 * @var $option_name                string
	 * @var $value                      string
	 * @var $this_widget                OptionsWidget
	 * @var $behavior                   OptionsBehavior
	 */
	$delimage_link_anchor = Yii::t( 'app', 'Delete image' );
	$view                 = $this_widget->getView();
	
	$delimage_script = <<<JS
		$("#delimg-{$option_name}-{$this_widget->model->id}").on('click', function(e) {
			e.preventDefault();
			var option_id = "{$optionList->id}";
			var model_id = "{$this_widget->model->id}";
			var model_name = "{$behavior->model_name}";
			var url = '/options/delimage';
			$.ajax(
				url,
				{
					type: 'POST',
					dataType: 'json',
					data: {
					    model_id: model_id, 
					    model_name: model_name, 
					    option_id: option_id
					},
					success: function(data) {
						if(data.success) {
							$("#img-{$option_name}-{$this_widget->model->id}").remove();
							$("#linkblock-{$option_name}-{$this_widget->model->id}").remove();
						}
					}
				}
			)
	})
JS;
?>
<div style="margin-bottom: 20px; padding: 5px; border: 1px solid rgba(166, 166, 166, 0.71)">
	<label>&nbsp; <?=$optionList->name?></label><br>
	<? // Если есть изображение, то показываем его Show image if exist
		if ( MyHelper::IFF( $value ) ):?>
			<?=Html::img(
				$value, [
					'style' => 'width: 100px; height:auto;',
					'id'    => "img-{$option_name}-{$this_widget->model->id}",
				]
			);?>
			
			<div id="linkblock-<?=$option_name?>-<?=$this_widget->model->id?>">
				<a href="" id="delimg-<?=$option_name?>-<?=$this_widget->model->id?>"><?=$delimage_link_anchor?></a>
			</div>
			<? $view->registerJs( $delimage_script, $view::POS_READY, "delimg-{$option_name}-{$this_widget->model->id}" ); ?>
		<? endif; ?>
	<?=Html::fileInput( $option_name );?>
</div>