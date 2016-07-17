<?
	use yii\helpers\Html;
	use porcelanosa\yii2options\models\OptionsList;
	
	/**
	 * @var $optionList          OptionsList
	 * @var $option_name         string
	 * @var $multipleValuesArray array
	 * @var $status_preset_items array
	 *
	 */

?>

<div style="margin-bottom: 20px; padding: 5px; border: 1px solid rgba(166, 166, 166, 0.71)">
	<label>&nbsp;<?=$optionList->name?></label><br>
	<?=Html::checkboxList(
		$option_name,
		$multipleValuesArray,
		$status_preset_items,
		[
			'id'          => $option_name,
			'class'       => 'form-control',
			'multiple'    => 'true',
			'itemOptions' => [ 'class' => 'i-check', 'style' => 'display:inline-block' ]
		]
	);
	?>
</div>