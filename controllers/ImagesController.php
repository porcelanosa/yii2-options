<?php
    namespace porcelanosa\yii2options\controllers;
    
    use common\models\Cats;
    use porcelanosa\yii2options\models\OptionPresets;
    use porcelanosa\yii2options\models\OptionPresetValues;
    use vova07\imperavi\actions\GetAction;
    use yii\web\Controller;
    use yii\web\ForbiddenHttpException;
    use Yii;
    
    class ImagesController extends Controller
    {
        
        public function actions()
        {
            /*$model = new Cats();
            $behavior = $model->getBehavior('optionsBehavior');*/
            $module    = Yii::$app->getModule('options');
            $fileUrl   = $module->fileUrl;
            $filePath  = $module->filePath;
            $imageUrl  = $module->imageUrl;
            $imagePath = $module->imagePath;
            
            return [
                'images-get'   => [
                    'class' => 'vova07\imperavi\actions\GetAction',
                    'url'   => $imageUrl, // Directory URL address, where files are stored.
                    'path'  => $imagePath, // Or absolute path to directory where files are stored.
                    'type'  => GetAction::TYPE_IMAGES,
                ],
                'files-get'    => [
                    'class' => 'vova07\imperavi\actions\GetAction',
                    'url'   => $fileUrl, // Directory URL address, where files are stored.
                    'path'  => $filePath, // Or absolute path to directory where files are stored.
                    'type'  => GetAction::TYPE_FILES,
                ],
                'image-upload' => [
                    'class' => 'vova07\imperavi\actions\UploadAction',
                    'url'   => $imageUrl, // Directory URL address, where files are stored.
                    'path'  => $imagePath // Or absolute path to directory where files are stored.
                ],
                'file-upload'  => [
                    'class'           => 'vova07\imperavi\actions\UploadAction',
                    'url'             => $fileUrl, // Directory URL address, where files are stored.
                    'path'            => $filePath, // Or absolute path to directory where files are stored.
                    'uploadOnlyImage' => false, // For not image-only uploading.
                ],
            ];
        }
        
    }