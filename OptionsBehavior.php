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
				$option_name     = trim( str_replace( ' ', '_', $option->alias ) );
				$option_type     = $option->type->alias;
				$postOptinonName = $_POST[ $option_name ] ? $_POST[ $option_name ] : 0;
				
				// Если есть изображение загружаем
				if ( $option_type == 'image' ) {
					$image = UploadedFile::getInstanceByName( $option_name );
					if ( $image ) {
						$filename = basename( $image->name, ".{$image->extension}" );
						
						// generate a unique file name
						$imageName = "{$filename}-" . Yii::$app->security->generateRandomString( 8 ) . ".{$image->extension}";
						
						$path = $this->uploadImagePath;
						$url = $this->uploadImageUrl;
						if ( ! is_dir( $path ) ) {
							mkdir( $path, 0777, true );
						}
						
						$fullPath = $path . $imageName;
						$fullUrl = $url . $imageName;
						if ( $image->saveAs( $fullPath) ) {
							$postOptinonName = $fullUrl;
						};
					}
				}
				// Есть ли такой статус ???
				$is_exist_status = $this->ifOptionExist( $option->id );
				
				if ( ! $is_exist_status ) { // ДОБАВЛЯЕМ если нет
					Yii::$app->db->createCommand()
					             ->insert(
						             'options',
						             [
							             'value'     => $postOptinonName,
							             'model'     => $this->model_name,
							             'model_id'  => $model->id,
							             'option_id' => $option->id,
						             ]
					             )->execute()
					;
					$last_insert_option_id = Yii::$app->db->getLastInsertID();
					$this->setMultipleOptions( $option_type, $option_name, $last_insert_option_id );
					// Сохранение richText and simple textarea
					if ( $option_type == 'richtext' OR $option_type == 'textarea' ) {
						$this->setRichtextOptions( $last_insert_option_id, $postOptinonName );
					}
					
				} else {
					// ОБНОВЛЯЕМ если есть
					Yii::$app->db->createCommand()
					             ->update(
						             'options',
						             [ 'value' => $postOptinonName ],
						             [
							             'model'     => $this->model_name,
							             'model_id'  => $model->id,
							             'option_id' => $option->id,
						             ]
					             )->execute()
					;
					$opt = Options::findOne(
						[
							'model_id'  => $model->id,
							'option_id' => $option->id,
						]
					);
					$this->setMultipleOptions( $option_type, $option_name, $opt->id );
					// Сохранение richText and simple textarea
					if ( $option_type == 'richtext' OR $option_type == 'textarea' ) {
						$this->setRichtextOptions( $option->id, $postOptinonName );
					}
				}
			}
		}
		
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
		
		protected function setRichtextOptions( $option_id, $text ) {
			
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
		 * Определяем есть ли такой статус и если есть, то возвращаем его
		 *
		 * @param $option_id integer
		 *
		 * @return mixed
		 */
		public function getOptionValueById(
			$option_id
		) {
			
			$option = ( new \yii\db\Query() )
				->select( 'value' )
				->from( 'options' )
				->where(
					[
						'model_id'  => $this->owner->id,
						'model'     => $this->model_name,
						'option_id' => $option_id
					]
				)->one()
			;
			
			return $option['value'];
			
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
			$option     = Options::find()
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
		 * Определяем есть ли у работы такой статус и если есть, то возвращаем его
		 *
		 * @param $option_id integer
		 *
		 * @return mixed
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
		 * Определяем есть ли у работы этот статус ВООБЩЕ
		 *
		 * @param $status_name
		 *
		 * @return boolean
		 */
		public function ifOptionExist(
			$option_id
		) {
			
			$option = ( new \yii\db\Query() )
				->select( [ 'value' ] )
				->from( 'options' )
				->where(
					[
						'model'     => $this->model_name,
						'model_id'  => $this->owner->id,
						'option_id' => $option_id
					]
				)
				->one()
			;
			
			if ( count( $option['value'] ) == 0 ) {
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
		 * @return \yii\db\ActiveQuery
		 */
		public function getOptionsList() {
			$options = OptionsList::find()
			                      ->where(
				                      [
					                      'model' => $this->model_name,
					                      //'model_id' => $this->id
				                      ]
			                      )
			                      ->all()
			;
			
			return $options;
		}
		
	}