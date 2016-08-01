<?php
    namespace porcelanosa\yii2options;
    
    use porcelanosa\yii2options\assets\OptionsAsset;
    use porcelanosa\yii2options\components\helpers\MyHelper;
    use porcelanosa\yii2options\models\ChildOptions;
    use porcelanosa\yii2options\models\ChildRichTexts;
    use porcelanosa\yii2options\models\OptionPresetValues;
    use porcelanosa\yii2options\models\Options;
    use porcelanosa\yii2options\models\RichTexts;
    use Yii;
    use yii\base\Exception;
    use yii\base\Widget;
    use yii\db\ActiveRecord;
    use yii\helpers\ArrayHelper;
    use yii\helpers\Json;
    use yii\helpers\Url;
    use yii\helpers\Html;
    
    use vova07\imperavi\Widget as Redactor;
    
    /**
     * Widget to Options Behavior
     *
     * @author Porcelanosa
     */
    class ChildOptionsWidget extends Widget
    {
        /** @var ActiveRecord */
        public $model;
        
        /** @var string */
        public $behaviorName;
        
        /** @var ChildOptionsBehavior Model of gallery to manage */
        protected $behavior;
        
        public $options = array();
        
        public $options_string = '';
        
        public function init()
        {
            parent::init();
            $this->behavior = $this->model->getBehavior($this->behaviorName);
            //$this->registerTranslations();
        }
        
        public function registerTranslations()
        {
            $i18n                                   = Yii::$app->i18n;
            $i18n->translations['galleryManager/*'] = [
                'class'          => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en-US',
                'basePath'       => '@zxbodya/yii2/galleryManager/messages',
                'fileMap'        => [],
            ];
        }
        
        
        /** Render widget */
        public function run()
        {
            
            $model             = $this->behavior->owner;
            $model_name        = MyHelper::modelFromNamespace($this->behavior->model_name);
            $parent_model_name = MyHelper::modelFromNamespace($this->behavior->parent_model_name);
            $parent_relation   = $this->behavior->parent_relation;
            if ($model->{$parent_relation}) {
                $cat_id = $model->{$parent_relation}->id;
                
                /**
                 * Формируем поля для параметров
                 *
                 * @var $optionList \porcelanosa\yii2options\models\OptionsList
                 */
                if ($this->behavior->getChildOptionsList($cat_id) AND is_array($this->behavior->getChildOptionsList($cat_id))) {
                    foreach ($this->behavior->getChildOptionsList($cat_id) as $optionList) {
                        $option = ChildOptions::findOne([
                            'model'     => $parent_model_name . '-' . $model_name,
                            'model_id'  => $model->id,
                            'option_id' => $optionList->id
                        ]);
                        
                        $option_name       = trim(str_replace(' ', '_', $optionList->alias));
                        $value             = $this->behavior->getChildOptionValueById($optionList->id);
                        $option_type_alias = $optionList->type->alias;
                        
                        if ($option_type_alias == 'boolean') {
                            $this->options_string .=
                                $this->render(
                                    '@vendor/porcelanosa/yii2-options/views/_partials/_boolean',
                                    [
                                        'option_name' => $option_name,
                                        'optionList'  => $optionList,
                                        'value'       => $value,
                                    ]
                                );
                        }
                        if ($option_type_alias == 'textinput') {
                            $this->options_string .=
                                $this->render(
                                    '@vendor/porcelanosa/yii2-options/views/_partials/_textinput',
                                    [
                                        'option_name' => $option_name,
                                        'optionList'  => $optionList,
                                        'value'       => $value,
                                    ]
                                );
                        }
                        if ($option_type_alias == 'textarea') {
                            $textarea = $option ? ChildRichTexts::find()->where(['option_id' => $option->id])->one() : null;
                            $this->options_string .=
                                $this->render(
                                    '@vendor/porcelanosa/yii2-options/views/_partials/_textarea',
                                    [
                                        'option_name'   => $option_name,
                                        'optionList'    => $optionList,
                                        'richTextValue' => $textarea != null ? $textarea->text : '',
                                    ]
                                );
                        }
                        if ($option_type_alias == 'richtext') {
                            
                            $richText = $option ? ChildRichTexts::find()->where(['option_id' => $option->id])->one() : null;
                            $this->options_string .=
                                $this->render(
                                    '@vendor/porcelanosa/yii2-options/views/_partials/_rich_text',
                                    [
                                        'option_name'   => $option_name,
                                        'optionList'    => $optionList,
                                        'richTextValue' => $richText != null ? $richText->text : '',
                                    ]
                                );
                        }
                        if ($option_type_alias == 'dropdown') {
                            // получаем фабрики
                            $status_preset_values =
                                OptionPresetValues::find()->where(['preset_id' => $optionList->preset->id])->orderBy('sort')->all();
                            // формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
                            $status_preset_items = ArrayHelper::map($status_preset_values, 'id', 'value');
                            $status_preset_items =
                                ArrayHelper::merge(['null' => 'Выберите ' . mb_strtolower($optionList->name)],
                                    $status_preset_items);
                            $this->options_string .=
                                $this->render(
                                    '@vendor/porcelanosa/yii2-options/views/_partials/_dropdown',
                                    [
                                        'option_name'         => $option_name,
                                        'optionList'          => $optionList,
                                        'value'               => $value,
                                        'status_preset_items' => $status_preset_items,
                                    ]
                                );
                        }
                        if ($option_type_alias == 'radiobuton_list') {
                            
                            // получаем фабрики
                            $status_preset_values =
                                OptionPresetValues::find()->where(['preset_id' => $optionList->preset->id])->orderBy('sort')->all();
                            // формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
                            $status_preset_items = ArrayHelper::map($status_preset_values, 'id', 'value');
                            $this->options_string .=
                                '<label>&nbsp;' . $optionList->name . '</label>' .
                                Html::radioList($option_name, $value ? $value : null, $status_preset_items, [
                                    'id'    => $option_name,
                                    'class' => 'form-control'
                                ]);
                        }
                        if ($option_type_alias == 'dropdown-multiple') {
                            //  получаем список значений для мульти селектед
                            $multipleValuesArray = $this->behavior->getChildOptionMultipleValueByOptionId($option->id);
                            // получаем фабрики
                            $status_preset_values =
                                OptionPresetValues::find()->where(['preset_id' => $optionList->preset->id])->orderBy('sort')->all();
                            // формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
                            $status_preset_items = ArrayHelper::map($status_preset_values, 'id', 'value');
                            
                            $this->options_string .=
                                $this->render(
                                    '@vendor/porcelanosa/yii2-options/views/_partials/_dropdown_multiple',
                                    [
                                        'option_name'         => $option_name,
                                        'optionList'          => $optionList,
                                        'multipleValuesArray' => $multipleValuesArray,
                                        'status_preset_items' => $status_preset_items,
                                    ]
                                );
                        }
                        /*  Список checkboxes  */
                        if ($option_type_alias == 'checkboxlist-multiple') {
                            //  получаем список значений для мульти селектед
                            $multipleValuesArray = $option ? $this->behavior->getChildOptionMultipleValueByOptionId(
                                $option->id
                            ) : [];
                            // получаем фабрики
                            $status_preset_values =
                                OptionPresetValues::find()->where(['preset_id' => $optionList->preset->id])->orderBy('sort')->all();
                            // формируем массив, с ключем равным полю 'id' и значением равным полю 'name'
                            $status_preset_items = ArrayHelper::map($status_preset_values, 'id', 'value');
                            $this->options_string .=
                                $this->render(
                                    '@vendor/porcelanosa/yii2-options/views/_partials/_checkboxlist_multiple',
                                    [
                                        'option_name'         => $option_name,
                                        'optionList'          => $optionList,
                                        'multipleValuesArray' => $multipleValuesArray,
                                        'status_preset_items' => $status_preset_items,
                                    ]
                                );
                        }
                        /*  IMAGE Изображение */
                        if ($option_type_alias == 'image') {
                            $this->options_string .=
                                $this->render(
                                    '@vendor/porcelanosa/yii2-options/views/_partials/_image',
                                    [
                                        'option_name' => $option_name,
                                        'optionList'  => $optionList,
                                        'value'       => $value,
                                        'this_widget' => $this,
                                        'behavior'    => $this->behavior,
                                    ]
                                );
                        }
                    }
                }
                
                $view = $this->getView();
                OptionsAsset::register($view);
                
                $this->options['id']    = 'child-opt-widget-' . $this->model->id;
                $this->options['class'] = 'child-options';
            }
            
            return $this->render('optionsWidget', ['options_string' => $this->options_string]);
        }
        
    }
