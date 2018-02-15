File uploader for Yii2
======================
File uploader for Yii2

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist fabriziocaldarelli/yii2-file-uploader "*"
```

or add

```
"fabriziocaldarelli/yii2-file-uploader": "*"
```

to the require section of your `composer.json` file.


Configuration
-----

Once the extension is installed, configure it in config\main.php setting imageBaseUrl, fileUploadBasePath and fileUploadBaseUrl :

```php
'modules' => [
    'fileUploader' => [
        'class' => 'sfmobile\ext\fileUploader\Module',

        // Customize properties
        'imagesBaseUrl' => 'http://path.to.your.user/',
        'fileUploadBasePath' =>  '/var/www/vhosts/your_hosting/public_files',
        'fileUploadBaseUrl' =>  '/public_files',
        
        'dbTableName' => 'file_upload',

    ], 
],    
```

Then add the module in bootstrap section of config\main.php

```
'bootstrap' => ['log', 'fileUploader'],
```


Usage
-----

Finally, inside view file insert code to show Kartik File Input widget:

```php
<?= \sfmobile\ext\fileUploader\components\kartikFileInput\KartikFileInput::widget(['model' => $model, 'modelName' => 'nameOfModelClass', 'attributeName' => 'attributeNameOfModelClass', 'acceptedTypes' => 'image/*', 'maxFileCount' => 999]); ?> 
```

and inside the controller change standard actionCreate as:

```
    public function actionCreate()
    {
        $model = new Model();
        
        \sfmobile\ext\fileUploader\models\FileInSession::initFromModelOrCreateFromForm('nameOfModelClass', 'attributeNameOfModelClass', $model->filesOfAttributeName);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        	
            // Sync files
            \sfmobile\ext\fileUploader\models\FileUpload::syncFilesFromSessiondAndRemoveFromSession('nameOfModelClass', 'attributeNameOfModelClass', 'section', 'category', \Yii::$app->user->identity->id, [ 'refer_id' => $model->id ]);             
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
```

and standard actionUpdate as:

```
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        \sfmobile\ext\fileUploader\models\FileInSession::initFromModelOrCreateFromForm('nameOfModelClass', 'attributeNameOfModelClass', $model->filesOfAttributeName);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            
            // Sync files
            \sfmobile\ext\fileUploader\models\FileUpload::syncFilesFromSessiondAndRemoveFromSession('nameOfModelClass', 'attributeNameOfModelClass', 'section', 'category', \Yii::$app->user->identity->id, [ 'refer_id' => $model->id ]);            
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
```
