<?php
    namespace porcelanosa\yii2options;
    
    use porcelanosa\yii2options\components\helpers\MyHelper;
    use common\models\Cats;
    use porcelanosa\yii2options\models\ChildOptionMultiple;
    use porcelanosa\yii2options\models\ChildOptions;
    use porcelanosa\yii2options\models\ModelsOptionsList;
    use porcelanosa\yii2options\models\OptionMultiple;
    use porcelanosa\yii2options\models\OptionPresetValues;
    use porcelanosa\yii2options\models\Options;
    use porcelanosa\yii2options\models\OptionsList;
    use porcelanosa\yii2options\models\ChildRichTexts;
    use Yii;
    use yii\base\InvalidConfigException;
    use yii\behaviors\AttributeBehavior;
    
    use yii\db\ActiveRecord;
    use yii\helpers\ArrayHelper;
    use yii\web\Controller;
    use yii\helpers\Html;
    
    
    use yii\web\UploadedFile;
    use yii\web\View;
    
    /*
     *
     * */
    
    class ChildOptionsBehavior
        extends AttributeBehavior
    {
        
        public $model_name = 'Items';
        public $options_string = '';
        public $parent_model_name = 'Cats';
        public $parent_relation = 'cat';
        public $uploadImagePath = ''; // '@webroot/uploads/cats/' alias of upload folder
        public $uploadImageUrl = ''; // '@web/uploads/cats/' alias of upload folder;
        public $appUrl;
        
        public function events()
        {
            return [
                ActiveRecord::EVENT_BEFORE_UPDATE => 'saveOptions',
            ];
        }
        
        
        public function saveOptions()
        {
            $model_name        = MyHelper::modelFromNamespace($this->model_name);
            $parent_model_name = MyHelper::modelFromNamespace($this->parent_model_name);
            $model             = $this->owner;
            $cat_id            = $model->{$this->parent_relation}->id;
            if ( ! isset($this->uploadImagePath) || $this->uploadImagePath == '') {
                throw new InvalidConfigException(
                    "The 'uploadImagePath' option is required. For example, ',
					'uploadImagePath' => '@webroot/uploads/cats/'"
                );
            }
            if ( ! isset($this->uploadImageUrl) || $this->uploadImageUrl == '') {
                throw new InvalidConfigException(
                    "The 'uploadImageUrl' option is required. For example, ',
					'uploadImageUrl' => '@web/uploads/cats/'"
                );
            }
            
            //  обрабатываем поля статусов
            foreach ($this->getChildOptionsList($cat_id) as $option) {
                $option_name       = trim(str_replace(' ', '_', $option->alias));
                $option_type_alias = $option->type->alias;
                $post_value        = Yii::$app->request->post($option_name); // POST value - переданное значение
                
                if (null != $post_value) {
                    $postOptionName = $post_value != '' ? $post_value : '';
                } else {
                    $postOptionName = null;
                }
                // If empty value for checkbox options set 0(zero) value option
                if ($postOptionName == null AND $option_type_alias == 'boolean') {
                    $postOptionName = 0;
                }
                // If empty value for multiple options delete option
                if ($postOptionName == null AND in_array($option_type_alias,
                        MyHelper::TYPES_WITH_MULTIPLE_PRESET_ARRAY)
                ) {
                    /**
                     * Find old options for delete
                     *
                     * @var $for_delete_opt ChildOptions
                     */
                    $for_delete_opt = ChildOptions::find()->where(
                        [
                            'model'     => $parent_model_name . '-' . $model_name,
                            'model_id'  => $model->id,
                            'option_id' => $option->id,
                        ]
                    )->one()
                    ;
                    if ($for_delete_opt) {
                        $curent_options = ChildOptionMultiple::find()->where(['option_id' => $for_delete_opt->id])->all();
                        
                        foreach ($curent_options as $c_opt) {
                            $c_opt->delete();
                        }
                    }
                    // Удаляем, если нашли // Delete if exist
                    if ($for_delete_opt) {
                        $for_delete_opt->delete();
                    }
                }
                // Есть ли такой статус ???
                $is_exist_status = $this->ifOptionExist($option->id);
                // Если есть изображение загружаем
                if ($option_type_alias == 'image') {
                    $image = UploadedFile::getInstanceByName($option_name);
                    if ($image) {
                        $old_image = ($is_exist_status) ? $this->getChildOptionValueByAlias($option_name) : '';
                        
                        $filename = basename($image->name, ".{$image->extension}");
                        
                        // generate a unique file name
                        $imageName = "{$filename}-" . Yii::$app->security->generateRandomString(8) . ".{$image->extension}";
                        
                        $path = $this->uploadImagePath;
                        $url  = $this->uploadImageUrl;
                        if ( ! is_dir($path)) {
                            mkdir($path, 0777, true);
                        }
                        
                        $fullPath = $path . $imageName;
                        $fullUrl  = $url . $imageName;
                        if ($image->saveAs($fullPath)) {
                            $postOptionName = $fullUrl;
                            /* delete old image */
                            $old_image_path = $path . str_replace($url, '', $old_image);
                            if (file_exists($old_image_path) AND is_file($old_image_path)) {
                                unlink($old_image_path);
                            }
                        };
                    }
                }
                if ( ! $is_exist_status && isset($postOptionName)) {
                    // ДОБАВЛЯЕМ если нет
                    $current_opt            = new ChildOptions();
                    $current_opt->value     = is_array($postOptionName) ? $postOptionName[0] : $postOptionName;
                    $current_opt->model     = $parent_model_name . '-' . $model_name;
                    $current_opt->model_id  = $model->id;
                    $current_opt->option_id = $option->id;
                    if ($current_opt->save()) {
                        // Сохранение полей с множеством значений
                        if (in_array($option_type_alias, MyHelper::TYPES_WITH_MULTIPLE_PRESET_ARRAY)) {
                            $this->setMultipleOptions($option_type_alias, $option_name, $current_opt->id);
                        }
                        // Сохранение richText and simple textarea
                        if ($option_type_alias == 'richtext' OR $option_type_alias == 'textarea') {
                            $this->setRichtextOptions($postOptionName, $current_opt->id);
                        }
                    }
                } elseif (isset($postOptionName)) {
                    //var_dump($postOptionName);
                    /**
                     * @var $current_opt ChildOptions
                     */
                    $current_opt        = ChildOptions::find()->where(
                        [
                            'model'     => $parent_model_name . '-' . $model_name,
                            'model_id'  => $model->id,
                            'option_id' => $option->id,
                        ]
                    )->one()
                    ;
                    $current_opt->value = is_array($postOptionName) ? (string)$postOptionName[0] : (string)$postOptionName;
                    //var_dump($current_opt->id.'--'.$current_opt->value);
                    if ($current_opt->save()) {
                        // Сохранение полей с множеством значений
                        if (in_array($option_type_alias, MyHelper::TYPES_WITH_MULTIPLE_PRESET_ARRAY)) {
                            $this->setMultipleOptions($option_type_alias, $option_name, $current_opt->id);
                        }
                        // Сохранение richText and simple textarea
                        if ($option_type_alias == 'richtext' OR $option_type_alias == 'textarea') {
                            $this->setRichtextOptions($postOptionName, $current_opt->id);
                        }
                    };
                }
            }
        }
        
        public function setMultipleOptions($option_type, $option_name, $option_id)
        {
            if (in_array($option_type, MyHelper::TYPES_WITH_MULTIPLE_PRESET_ARRAY)) {
                $option_array = Yii::$app->request->post($option_name);
                if (is_array($option_array)) {
                    // удаляем все значения с этим option_id
                    $curent_options = ChildOptionMultiple::find()->where(['option_id' => $option_id])->all();
                    foreach ($curent_options as $c_opt) {
                        $c_opt->delete();
                    }
                    foreach ($option_array as $option_value) {
                        $newOptionMultiple            = new ChildOptionMultiple();
                        $newOptionMultiple->option_id = $option_id;
                        $newOptionMultiple->value     = $option_value;
                        $newOptionMultiple->save();
                    }
                }
            }
        }
        
        /**
         * Определяем есть ли у работы такой статус и если есть, то возвращаем его
         *
         * @param $option_id integer
         *
         * @return mixed
         */
        public function getChildOptionValueById($option_id)
        {
            $model_name        = MyHelper::modelFromNamespace($this->model_name);
            $parent_model_name = MyHelper::modelFromNamespace($this->parent_model_name);
            $option            = (new \yii\db\Query())
                ->select('value')
                ->from('child_options')
                ->where(
                    [
                        'model_id'  => $this->owner->id,
                        'model'     => $parent_model_name . '-' . $model_name,
                        'option_id' => $option_id
                    ]
                )->one()
            ;
            //var_dump($cats_statuses['value']);
            
            /*if (is_null($works_statuses['value'])) {
                return false;
            } else {
            }*/
            
            return $option['value'];
            
        }
        
        /**
         * Определяем есть ли у работы такой статус и если есть, то возвращаем его
         *
         * @param $option_id integer
         *
         * @return mixed
         */
        public function getChildOptionValueByOptionId($option_id)
        {
            
            $option = (new \yii\db\Query())
                ->select('value')
                ->from('childe_options')
                ->where(
                    [
                        'option_id' => $option_id
                    ]
                )->one()
            ;
            //var_dump($cats_statuses['value']);
            
            /*if (is_null($works_statuses['value'])) {
                return false;
            } else {
            }*/
            
            return $option['value'];
            
        }
        
        /**
         * Определяем есть ли у работы такой статус и если есть, то возвращаем его
         *
         * @param $option_id integer
         *
         * @return mixed
         */
        public function getChildOptionMultipleValueByOptionId($option_id)
        {
            $return_array = [];
            $options      = ChildOptionMultiple::find()
                                               ->select('value')
                                               ->where(
                                                   [
                                                       'option_id' => $option_id
                                                   ]
                                               )->asArray()->all()
            ;
            foreach ($options as $option) {
                $return_array[] = $option['value'];
            }
            
            return $return_array;
            
        }
        
        /**
         * Определяем есть ли у работы этот статус ВООБЩЕ
         *
         * @param $status_name
         *
         * @return boolean
         */
        public function ifOptionExist($option_id)
        {
            $model_name        = MyHelper::modelFromNamespace($this->model_name);
            $parent_model_name = MyHelper::modelFromNamespace($this->parent_model_name);
            $option            = (new \yii\db\Query())
                ->select(['value'])
                ->from('child_options')
                ->where(
                    [
                        'model'     => $parent_model_name . '-' . $model_name,
                        'model_id'  => $this->owner->id,
                        'option_id' => $option_id
                    ]
                )
                ->one()
            ;
            
            if (count($option['value']) == 0) {
                return false;
            } else {
                return true;
            }
            
        }
        
        
        /**
         * @return OptionsList
         */
        public function getChildOptionsList($cat_id)
        {
            $model_name        = MyHelper::modelFromNamespace($this->model_name);
            $parent_model_name = MyHelper::modelFromNamespace($this->parent_model_name);
            $model             = new $this->parent_model_name();
            $parent_ids        = $this->getParentIds($cat_id, [$cat_id], $model);
            $option_ids        = ModelsOptionsList::find()
                                                  ->select('option_id')
                                                  ->where(
                                                      [
                                                          'IN',
                                                          'model_id',
                                                          $parent_ids
                                                      ]
                                                  )->asArray()->all()
            ;
            
            $options = OptionsList::find()
                                  ->where(
                                      [
                                          "IN",
                                          'id',
                                          $this->flatArray($option_ids, 'option_id')
                                      ]
                                  )
                                  ->andWhere(['model' => $parent_model_name . '-' . $model_name,])
                                  ->all()
            ;
            
            return $options;
        }
        
        public function flatArray($arr, $value_name)
        {
            $r_arr = [];
            foreach ($arr as $key => $value) {
                $r_arr[] = $value[$value_name];
            }
            
            return $r_arr;
        }
        
        /**
         * @var $model  ActiveRecord
         * @var $r_arr  array
         * @var $cat_id integer
         *
         * @return array
         */
        public function getParentIds($cat_id, $r_arr, $model)
        {
            
            /**
             * @var $model Cats
             */
            $this_model = $model::findOne(['id' => $cat_id]);
            if ($this_model) {
                if ($this_model->parent) {
                    
                    $parent_model = $model::findOne(['id' => $this_model->parent->id]);
                    
                    $parent_id            = $parent_model->parent ? $parent_model->parent->id : null;
                    $this_model_parent_id = $this_model->parent ? $this_model->parent->id : null;
                    
                    if ($parent_id != 0 || $parent_id != null) {
                        $r_arr = ArrayHelper::merge($r_arr, [$this_model_parent_id, $parent_id]);
                        $r_arr = ArrayHelper::merge($r_arr, $this->getParentIds($parent_id, $r_arr, $model));
                    } else {
                        $r_arr[] = $parent_model->id;
                    }
                }
            }
            
            return $r_arr;
        }
        
        /**
         * Получаем значение параметра конкретной модели по алиасу
         *
         * @param $alias                string string
         * @param $relations_model_name string
         *
         * @return mixed
         */
        public function getChildOptionValueByAlias($alias, $relations_model_name = 'Cats-Items')
        {
            /**
             * @var $optionList OptionsList
             * @var $option     Options
             */
            $optionList = OptionsList::find()->where(['alias' => $alias])->one();
            $option     = ChildOptions::find()
                                      ->where(
                                          [
                                              'model_id'  => $this->owner->id,
                                              'model'     => $relations_model_name,
                                              'option_id' => $optionList->id
                                          ]
                                      )->one()
            ;
            if (in_array($optionList->type->alias, MyHelper::TYPES_WITH_PRESET_ARRAY)) {
                $return = $optionList->preset->value($option->value);
            } elseif (in_array($optionList->type->alias, MyHelper::TYPES_WITH_MULTIPLE_PRESET_ARRAY)) {
                $optionM = ChildOptionMultiple::find()->where(['value' => $option->value])->one();
                $return  = $optionList->preset->value($optionM->value);
            } else {
                $return = $option->value;
            }
            
            return $return;
            
        }
        
        /**
         * @param $text      string
         * @param $option_id integer
         */
        protected function setRichtextOptions($text, $option_id)
        {
            
            // удаляем все значения с этим option_id
            $currentRichText = ChildRichTexts::find()->where(['option_id' => $option_id])->one();
            if ($currentRichText) {
                $currentRichText->updateAttributes(['text' => $text]);
            } else {
                $richText            = new ChildRichTexts();
                $richText->option_id = $option_id;
                $richText->text      = $text;
                $richText->save();
            }
            
        }
    }