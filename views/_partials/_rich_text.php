<?
    use yii\helpers\Html;
    use vova07\imperavi\Widget as Redactor;
    use porcelanosa\yii2options\models\OptionsList;
    use \yii\helpers\Url;
    
    /**
     * @var $optionList    OptionsList
     * @var $option_name   string
     * @var $richTextValue string
     * @var $behavior \porcelanosa\yii2options\OptionsBehavior
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
    )?>
    <?=Redactor::widget(
        [
            'selector' => '#' . $option_name,
            'settings' => [
                'lang'             => 'ru',
                'minHeight'        => 200,
                'maxHeight'        => 600,
                'structure'        => true,
                'imageManagerJson' => Url::to(['/options/images/images-get']),
                'fileManagerJson'  => Url::to(['/options/images/files-get']),
                'imageUpload'      => Url::to(['/options/images/image-upload']),
                'fileUpload'       => Url::to(['/options/images/file-upload']),
                'plugins'          => [
                    'clips',
                    'fullscreen'
                ]
            ]
        ]
    );
    ?>
</div>
		