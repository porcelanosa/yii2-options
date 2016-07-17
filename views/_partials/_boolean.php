<?
	use yii\helpers\Html;
	use porcelanosa\yii2options\models\OptionsList;
	
	/**
	 * @var $optionList          OptionsList
	 * @var $option_name         string
	 * @var $value               string
	 *
	 */

?>
<div class="checkbox">
	<label>
		<?=Html::checkbox(
			$option_name, $value ? $value : '', [
				'id'    => $option_name,
				'class' => 'i-check'
			]
		)?>
		&nbsp;<?=$optionList->name?>
	</label>
</div>