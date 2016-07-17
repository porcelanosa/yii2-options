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
	
	<label>&nbsp;<?=$optionList->name?></label>
<?=Html::textInput(
	$option_name,
	$value ? $value : '',
	[
		'id'    => $option_name,
		'class' => 'form-control'
	]
);
?>