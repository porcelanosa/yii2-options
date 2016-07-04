<?php
/**
 * @var $this View
 * @var $options_string string
 * @author Alex Porcelanosa
 */
use yii\helpers\Html;
use yii\web\View;

?>
<?php echo Html::beginTag('div', $this->context->options); ?>
    
        <?=$options_string?>
<?php echo Html::endTag('div'); ?>
