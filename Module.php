<?php
	
	/*
	 * This file is part of the Porcelanosa project.
	 *
	 * (c) Porcelanosa project <http://github.com/porcelanosa>
	 *
	 * For the full copyright and license information, please view the LICENSE.md
	 * file that was distributed with this source code.
	 */
	
	namespace porcelanosa\yii2options;
	
	use yii\base\Module as BaseModule;
	use yii\filters\AccessControl;
	
	/**
	 * @author Porcelanosa
	 */
	class Module extends BaseModule {
		/**
		 * @var bool Whether to show flash messages
		 */
		public $enableFlashMessages = true;
		
		/**
		 * @var string
		 */
		public $defaultRoute = 'optionslist/index';
		
		/**
		 * @var array
		 */
		public $admins = [ ];
		
		/**
		 * @var string The Administrator permission name.
		 */
		public $adminPermission;
		
		/**
		 * @var string path to models for binding options
		 */
		public $model_path = '';
		/**
		 * @var string model namespace
		 */
		public $modelNamespace = '';
		/** @inheritdoc */
		public function behaviors() {
			return [
				'access' => [
					'class' => AccessControl::className(),
					'rules' => [
						[
							'allow'         => true,
							'roles'         => [ '@' ],
							'matchCallback' => [ $this, 'checkAccess' ],
						]
					],
				],
			];
		}
		
		/**
		 * Checks access.
		 *
		 * @return bool
		 */
		public function checkAccess() {
			$user = \Yii::$app->user->identity;
			
			if ( method_exists( $user, 'getIsAdmin' ) ) {
				return $user->getIsAdmin();
			} else if ( $this->adminPermission ) {
				return $this->adminPermission ? \Yii::$app->user->can( $this->adminPermission ) : false;
			} else {
				return isset( $user->username ) ? in_array( $user->username, $this->admins ) : false;
			}
		}
	}
