<?php
	namespace porcelanosa\yii2options;
	
	use porcelanosa\yii2options\components\helpers\MyHelper;
	use porcelanosa\yii2options\models\Options;
	use porcelanosa\yii2options\models\OptionsList;
	use porcelanosa\yii2options\models\OptionMultiple;
	use porcelanosa\yii2options\models\OptionPresetValues;
	use porcelanosa\yii2options\models\RichTexts;
	use Yii;
	use yii\base\InvalidConfigException;
	use yii\behaviors\AttributeBehavior;
	
	use yii\db\ActiveRecord;
	
	
	use yii\db\Exception;
	use yii\helpers\ArrayHelper;
	use yii\web\UploadedFile;
	
	/*
	 *
	 * */
	
	class OptionsBehavior
		extends AttributeBehavior {
		
		public $model_name = '';
		public $uploadImagePath = ''; // '@webroot/uploads/cats/' alias of upload folder
		public $uploadImageUrl = ''; // '@web/uploads/cats/' alias of upload folder
		
		public function events() {
			return [
				ActiveRecord::EVENT_BEFORE_UPDATE => 'saveOptions',
			];
		}
		
		public function saveOptions() {
			
			$model = $this->owner;
			
			if ( ! isset( $this->uploadImagePath ) || $this->uploadImagePath == '' ) {
				throw new InvalidConfigException(
					"The 'uploadImagePath' option is required. For example, ',
					'uploadImagePath' => '@webroot/uploads/cats/'"
				);
			}
			if ( ! isset( $this->uploadImageUrl ) || $this->uploadImageUrl == '' ) {
				throw new InvalidConfigException(
					"The 'uploadImageUrl' option is required. For example, ',
					'uploadImageUrl' => '@web/uploads/cats/'"
				);
			}
			//  обрабатываем поля статусов
			foreach ( $this->getOptionsList() as $option ) {
				$option_name = trim( str_replace( ' ', '_', $option->alias ) );
				$option_type = $option->type->alias; //  option alias - псевдоним параметра
				$post_value  = Yii::$app->request->post( $option_name ); // POST value - переданное значение
				
				if ( NULL != $post_value ) {
					$postOptionName = $post_value != '' ? $post_value : '';
				} else {
					$postOptionName = NULL;
				}
				// If empty value for multiple options delete option
				if ( $postOptionName == NULL AND in_array( $option_type, MyHelper::TYPES_WITH_MULTIPLE_PRESET_ARRAY ) ) {
					/**
					 * @var $for_delete_opt Options
					 */
					$for_delete_opt = Options::find()->where(
						[
							'model'     => $this->model_name,
							'model_id'  => $model->id,
							'option_id' => $option->id,
						]
					)->one()
					;
					$curent_options = OptionMultiple::find()->where( [ 'option_id' => $for_delete_opt->id ] )->all();
					
					foreach ( $curent_options as $c_opt ) {
						$c_opt->delete();
					}
					// Удаляем, если нашли // Delete if exist
					if ( $for_delete_opt ) {
						$for_delete_opt->delete();
					}
				}
				// Есть ли такой статус ???
				$is_exist_status = $this->ifOptionExist( $option->id );
				
				// Если есть изображение загружаем
				if ( $option_type == 'image' ) {
					$image = UploadedFile::getInstanceByName( $option_name );
					if ( $image ) {
						$old_image = ( $is_exist_status ) ? $model->getOptionValueByAlias( $option_name ) : '';
						
						$filename = basename( $image->name, ".{$image->extension}" );
						
						// generate a unique file name
						$imageName = "{$filename}-" . Yii::$app->security->generateRandomString( 8 ) . ".{$image->extension}";
						
						$path = $this->uploadImagePath;
						$url  = $this->uploadImageUrl;
						if ( ! is_dir( $path ) ) {
							mkdir( $path, 0777, true );
						}
						
						$fullPath = $path . $imageName;
						$fullUrl  = $url . $imageName;
						if ( $image->saveAs( $fullPath ) ) {
							$postOptionName = $fullUrl;
							/* delete old image */
							$old_image_path = $path . str_replace( $url, '', $old_image );
							if ( file_exists( $old_image_path ) AND is_file( $old_image_path ) ) {
								unlink( $old_image_path );
							}
						};
					}
				}
				// ДОБАВЛЯЕМ Options если нет options
				if ( ! $is_exist_status && isset( $postOptionName ) ) {
					$current_opt            = new Options();
					$current_opt->value     = is_array( $postOptionName ) ? $postOptionName[0] : $postOptionName;
					$current_opt->model     = $this->model_name;
					$current_opt->model_id  = $model->id;
					$current_opt->option_id = $option->id;
					if ( $current_opt->save() ) {
						// Сохранение полей с множеством значений
						if ( in_array( $option_type, MyHelper::TYPES_WITH_MULTIPLE_PRESET_ARRAY ) ) {
							$this->setMultipleOptions( $option_type, $option_name, $current_opt->id );
						}
						// Сохранение richText and simple textarea
						if ( $option_type == 'richtext' OR $option_type == 'textarea' ) {
							$this->setRichtextOptions( $postOptionName, $current_opt->id );
						}
					};
					
				} // ОБНОВЛЯЕМ если есть
				elseif ( isset( $postOptionName ) ) {
					/**
					 * @var $current_opt Options
					 */
					$current_opt        = Options::find()->where(
						[
							'model'     => $this->model_name,
							'model_id'  => $model->id,
							'option_id' => $option->id,
						]
					)->one()
					;
					$current_opt->value = is_array( $postOptionName ) ? $postOptionName[0] : $postOptionName;
					if ( $current_opt->save() ) {
						//  Обновление полей с множественными значениями
						if ( in_array( $option_type, MyHelper::TYPES_WITH_MULTIPLE_PRESET_ARRAY ) ) {
							$this->setMultipleOptions( $option_type, $option_name, $current_opt->id );
						}
						// Обновление richText and simple textarea
						if ( $option_type == 'richtext' OR $option_type == 'textarea' ) {
							$this->setRichtextOptions( $postOptionName, $current_opt->id );
						}
					}
				}
			}
		}
		
		/**
		 * @param $option_type string
		 * @param $option_name string
		 * @param $option_id   integer
		 */
		protected function setMultipleOptions( $option_type, $option_name, $option_id ) {
			if ( in_array( $option_type, MyHelper::TYPES_WITH_MULTIPLE_PRESET_ARRAY ) ) {
				$option_array = Yii::$app->request->post( $option_name );
				if ( is_array( $option_array ) ) {
					// удаляем все значения с этим option_id
					/**
					 * @var $curent_options OptionMultiple[]
					 */
					$curent_options = OptionMultiple::find()->where( [ 'option_id' => $option_id ] )->all();
					foreach ( $curent_options as $c_opt ) {
						$c_opt->delete();
						/*
						 * @TODO Delete only necessary
						 * if ( ! in_array( $c_opt->value, $option_array ) ) {
						}*/
					}
					foreach ( $option_array as $option_value ) {
						$newOptionMultiple            = new OptionMultiple();
						$newOptionMultiple->option_id = $option_id;
						$newOptionMultiple->value     = $option_value;
						$newOptionMultiple->save();
					}
				}
			}
		}
		
		/**
		 * @param $text      string
		 * @param $option_id integer
		 */
		protected function setRichtextOptions( $text, $option_id ) {
			
			// удаляем все значения с этим option_id
			$currentRichText = RichTexts::find()->where( [ 'option_id' => $option_id ] )->one();
			if ( $currentRichText ) {
				$currentRichText->updateAttributes( [ 'text' => $text ] );
			} else {
				$richText            = new RichTexts();
				$richText->option_id = $option_id;
				$richText->text      = $text;
				$richText->save();
			}
			
		}
		
		
		/**
		 * Get value from Options model for current model instance by $option_id
		 *
		 * @param $option_id integer
		 *
		 * @return string
		 */
		public function getOptionValueById( $option_id ) {
			/**
			 * @var $option Options
			 */
			$option = Options::find()
			                 ->where(
				                 [
					                 'model_id'  => $this->owner->id,
					                 'model'     => $this->model_name,
					                 'option_id' => $option_id
				                 ]
			                 )->one()
			;
			
			return $option ? $option->value : '';
			
		}
		
		/**
		 * Получаем значение параметра конкретной модели по алиасу
		 *
		 * @param $alias string
		 *
		 * @return mixed
		 */
		public function getOptionValueByAlias( $alias ) {
			/**
			 * @var $optionList OptionsList
			 * @var $option     Options
			 */
			$optionList = OptionsList::find()->where( [ 'alias' => $alias ] )->one();
			if ( $optionList ) {
				$option = Options::find()
				                 ->where(
					                 [
						                 'model_id'  => $this->owner->id,
						                 'model'     => $this->model_name,
						                 'option_id' => $optionList->id
					                 ]
				                 )->one()
				;
				if ( in_array( $optionList->type->alias, MyHelper::TYPES_WITH_PRESET_ARRAY ) ) {
					$return = $optionList->preset->value( $option->value );
				} elseif ( in_array( $optionList->type->alias, MyHelper::TYPES_WITH_MULTIPLE_PRESET_ARRAY ) ) {
					$optionM = OptionMultiple::find()->where( [ 'value' => $option->value ] )->one();
					$return  = $optionList->preset->value( $optionM->value );
				} else {
					$return = $option->value;
				}
				
				return $return;
			} else {
				return false;
			}
		}
		
		/**
		 * Определяем есть ли такой статус и если есть, то возвращаем его
		 *
		 * @param $option_id integer
		 *
		 * @return mixed
		 */
		public function getOptionValueByOptionId(
			$option_id
		) {
			
			$option = ( new \yii\db\Query() )
				->select( 'value' )
				->from( 'options' )
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
		 * Возвращаем массив значений опции с множественным выбором. Т.е. если есть много значений возвращаем все в
		 * виде массива
		 *
		 * @param $option_id integer
		 *
		 * @return array
		 */
		public function getOptionMultipleValueByOptionId( $option_id ) {
			$return_array = [ ];
			$options      = OptionMultiple::find()
			                              ->select( 'value' )
			                              ->where(
				                              [
					                              'option_id' => $option_id
				                              ]
			                              )->asArray()->all()
			;
			
			foreach ( $options as $option ) {
				$return_array[] = $option['value'];
			}
			
			return $return_array;
			
		}
		
		/**
		 * Определяем есть ли у модели этот параметр ВООБЩЕ
		 *
		 * @param $option_id int
		 *
		 * @return boolean
		 */
		public function ifOptionExist( $option_id ) {
			
			$option = Options::find()
			                 ->select( [ 'value' ] )
			                 ->where(
				                 [
					                 'model'     => $this->model_name,
					                 'model_id'  => $this->owner->id,
					                 'option_id' => $option_id
				                 ]
			                 )
			                 ->asArray()
			                 ->one()
			;
			
			if ( $option == NULL ) {
				return false;
			} else {
				return true;
			}
			
		}
		
		/**
		 * Определяем значение статуса с Пресетом
		 *
		 * @param $status_name
		 *
		 * @return mixed
		 */
		public function getStatusValueByStatusIdDD(
			$status_id
		) {
			$value  = $this->getStatusValueByStatusId( $status_id );
			$status = ( new \yii\db\Query() )
				->select( '*' )
				->from( $this->statuses_table )
				->where( [ 'id' => $status_id ] )
				->one()
			;
			
			$preset_values =
				OptionPresetValues::find()->where( [ 'preset_id' => $status['preset_id'], 'id' => $value ] )->one();
			
			/*print($value);
			print($status['preset_id']);*/
			
			return $preset_values['value'];
			
			
		}
		
		
		/**
		 * @return OptionsList[]
		 */
		public function getOptionsList() {
			$options = OptionsList::find()
			                      ->where(
				                      [
					                      'model' => $this->model_name
				                      ]
			                      )
			                      ->all()
			;
			
			return $options;
		}
		
	}