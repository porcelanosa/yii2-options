**WARNING! UNDER DEVELOPMENT**
[![Total Downloads](https://poser.pugx.org/porcelanosa/yii2-options/downloads)](https://packagist.org/packages/porcelanosa/yii2-options)
Installation
============

This document will guide you through the process of installing yii2-options using **composer**. Installation is a quick and
easy several step process.

> **NOTE:** Before we start make sure that you have properly configured **db** application component.


Step 1: Download using composer
-------------------------------

Add yii2-options to the require section of your **composer.json** file:

```js
{
    "require": {
        "porcelanosa/yii2-options": "dev-master"
    }
}
```

And run following command to download extension using **composer**:

```bash
$ php composer.phar update
```

Step 2: Configure your application
----------------------------------

Add options module to both web and console config files as follows:

```php
...
'modules' => [
    ...
    'options' => [
        'class' => 'porcelanosa\yii2options\Module',
        'layout' => '@app/modules/admin/views/layouts/main',
        'model_path' => '@app/modules/admin/models/*.php', // models php files
        'modelNamespace' => 'app\modules\admin\models\\' // models namespace
    ],
    ...
],
...
```


Step 3: Updating database schema
--------------------------------
After you downloaded and configured yii2-options, the last thing you need to do is updating your database schema by applying
the migration:

```bash
$ php yii migrate/up --migrationPath=@vendor/porcelanosa/yii2-options/migrations
```

Menu items
```php

['label' => Yii::t('app', 'ADMIN_NAV_STATUS_TYPES'), 'url' => ['/options/optiontypes/index']],
['label' => Yii::t('app', 'ADMIN_NAV_OPTIONS_LIST'), 'url' => ['/options/optionslist/index']],
```

Step 4: Adjust models
---------------------
Add behavior
```php
use porcelanosa\yii2options\models\Options;
use porcelanosa\yii2options\OptionsBehavior;
use porcelanosa\yii2options\ChildOptionsBehavior;
use porcelanosa\yii2options\components\helpers\MyHelper;

public function behaviors()
{
    return [
        'optionsBehavior' => [
           'class' => OptionsBehavior::className(),
           'model_name' => MyHelper::modelFromNamespace($this::className()), // convert className to model name without namespace
           'uploadImagePath' => Yii::getAlias( '@webroot' ) . '/uploads/cats/', // alias of upload folder
           'uploadImageUrl' => Yii::getAlias( '@web' ) . '/uploads/cats/', // alias of upload folder
        ],
}
```
Add binding paramters
```php
public $modelFrontName = 'Категории'; //if not define $modelFrontName - not show in dropdown list in optionslist controller
		
public $childModels = [
    'Items'=>'Товары в категории',
];
```


Step 5: Show options in admin view
-----------------------------------
```php
<? echo
OptionsWidget::widget(
    [
        'model'        => $model,
        'behaviorName' => 'optionsBehavior'
    ] 
);
?>
```
