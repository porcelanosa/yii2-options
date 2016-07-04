Installation
============

This document will guide you through the process of installing yii2-options using **composer**. Installation is a quick and
easy three-step process.

> **NOTE:** Before we start make sure that you have properly configured **db** application component.


Step 1: Download using composer
-------------------------------

Add yii2-options to the require section of your **composer.json** file:

```js
{
    "require": {
        "dektrium/yii2-options": "dev-master"
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
$ php yii migrate/up --migrationPath=@yii/rbac/migrations
```
