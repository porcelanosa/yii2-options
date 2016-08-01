<?
    use yii\helpers\Html;
    use porcelanosa\yii2options\models\OptionsList;
    
    /**
     * @var $optionList    OptionsList
     * @var $option_name   string
     * @var $richTextValue string
     *
     */
?>
<label>&nbsp;<?=$optionList->name?></label>
<?=
    Html::textarea(
        $option_name,
        $richTextValue ? $richTextValue : '',
        [
            'id'    => $option_name,
            'class' => 'form-control'
        ]
    )
?>
<hr>
