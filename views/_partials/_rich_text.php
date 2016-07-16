<?
	use yii\helpers\Html;
	use vova07\imperavi\Widget as Redactor;
	use porcelanosa\yii2options\models\OptionsList;
	
	/**
	 * @var $optionList    OptionsList
	 * @var $option_name   string
	 * @var $richTextValue string
	 *
	 */
?>
<div id="rich-text-<?=$optionList->name?>">
	<label>&nbsp;<?=$optionList->name?></label>
	<?=Html::textarea(
		$option_name,
		$richTextValue,
		[
			'id' => $option_name
		]
	) ?>
	 <?=  Redactor::widget(
		   [
			   'selector' => '#' . $option_name,
			   'settings' => [
				   'lang'      => 'ru',
				   'minHeight' => 200,
				   'plugins'   => [
					   'clips',
					   'fullscreen'
				   ]
			   ]
		   ]
	   );
	?>
</div>
		