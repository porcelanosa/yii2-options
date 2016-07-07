<?
	/**
	 * @var $options_array array
	 */
	?>
	<?foreach ($options_array as $opt):?>
		<?=$opt['name']?>:<?=$opt['value']?><br>
	<?endforeach;?>