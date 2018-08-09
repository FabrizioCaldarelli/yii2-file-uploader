# THIS REPOSITORY IS ABANDONED
# THIS REPOSITORY IS ABANDONED
# THIS REPOSITORY IS ABANDONED
# THIS REPOSITORY IS ABANDONED
# THIS REPOSITORY IS ABANDONED

Go to https://github.com/FabrizioCaldarelli/yii2-file-upload






























File uploader for Yii2
======================

Single and Multiple file uploader handler for Yii2

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

**1) Add fileUploader module to config.php**

```php
'modules' => [
    'fileUploader' => [
        'class' => 'sfmobile\ext\fileUploader\Module',

        // Customize properties
        'fileUploadBasePath' =>  '/var/www/vhosts/your_hosting/public_files',
        'fileUploadBaseUrl' =>  '/public_files',

        'dbTableName' => 'tbl_file_upload',

        'isFileUploadBaseUrlAbsolute' => false,

    ],
],
```

**2) Add the module in bootstrap section of config\main.php**

```php
'bootstrap' => ['log', 'fileUploader'],
```

**3) Apply database migration**

```
yii migrate --migrationPath=@vendor/fabriziocaldarelli/yii2-file-uploader/migrations
```

Usage
-----

I suggest to create ModelForm class that extends Model class and add an attribute to prefix session key 
with a random string.

```php
<?php
namespace backend\models;

use yii\helpers\ArrayHelper;

class ModelForm extends \common\models\Model
{
    public $modelPrefixSessionKeyAttribute;

    public function rules()
    {
        return ArrayHelper::merge([
            [['modelPrefixSessionKeyAttribute'], 'safe'],    
        ]);
    }
```

Finally, inside view file insert code to show Kartik File Input widget:

```php
<?= \sfmobile\ext\fileUploader\components\kartikFileInput\KartikFileInput::widget([
    'model' => $model,  
    'attribute' => 'attributeNameOfModelClass', 
    'acceptedTypes' => 'application/pdf', 
    'maxFileCount' => 10,
    'prefixSessionKeyAttribute' => 'modelPrefixSessionKeyAttribute'
]); ?>
```

or inside an ActiveForm $form

```php
<?= $form->field($model, 'attributeNameOfModelClass')->widget(\sfmobile\ext\fileUploader\components\kartikFileInput\KartikFileInput::className(), [
    'acceptedTypes' => 'application/pdf',
    'maxFileCount' => 10,
    'prefixSessionKeyAttribute' => 'modelPrefixSessionKeyAttribute'    
]); ?>
```


and inside the controller change standard actionCreate as:

```php
    public function actionCreate()
    {
        $model = new ModelForm();

        \sfmobile\ext\fileUploader\models\FileInSession::initFromModelOrCreateFromForm($model, 'attributeNameOfModelClass', $model->filesOfAttributeName, ['prefixSessionKeyAttribute' => 'modelPrefixSessioneKeyAttribute')';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            // Sync files
            \sfmobile\ext\fileUploader\models\FileUpload::syncFilesFromSessiondAndRemoveFromSession($model, 'attributeNameOfModelClass', 'section', 'category', \Yii::$app->user->identity->id, [ 'refer_id' => $model->id ], ['prefixSessionKeyAttribute' => 'modelPrefixSessioneKeyAttribute');
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
```

and standard actionUpdate as:

```php
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->modelPrefixSessionKeyAttribute = ArrayHelper::getValue($_POST, 'modelPrefixSessionKeyAttribute', Yii::$app->getSecurity()->generateRandomString());

        \sfmobile\ext\fileUploader\models\FileInSession::initFromModelOrCreateFromForm($model, 'attributeNameOfModelClass', $model->filesOfAttributeName, ['prefixSessionKeyAttribute' => 'modelPrefixSessioneKeyAttribute')';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            // Sync files
            \sfmobile\ext\fileUploader\models\FileUpload::syncFilesFromSessiondAndRemoveFromSession($model, 'attributeNameOfModelClass', 'section', 'category', \Yii::$app->user->identity->id, [ 'refer_id' => $model->id ], ['prefixSessionKeyAttribute' => 'modelPrefixSessioneKeyAttribute');
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
```

Suggestions
-----

Now I tell you some suggestions to solve typical problems with upload file.

#### 1) Delete files from filesystem when delete models

Extend `tbl_file_upload` adding fields that refers to other table. If you have an `order` table, you can add "ref_order_id" to `tbl_file_upload`, that `SET NULL` in **upload** and **delete** cascade.
Finally, in common\models, create a subclass of `\sfmobile\ext\fileUploader\models\FileUpload`, such as

```php
<?php

namespace common\models;

use Yii;

class FileUpload extends \sfmobile\ext\fileUploader\models\FileUpload
{
    public static function deleteNotLinked()
    {
        $query = self::find()->andWhere([
            'ref_order_id' => null,
        ]);

        foreach ($query->each(10) as $f) {
            $f->delete();
        }
    }
}

```

When you will delete a record from `order` table with referred record in `tbl_file_upload`, record in `tbl_file_upload` will have `ref_order_id` set to **NULL**.
So, in Order model you should extend afterDelete method to launch deleteNotLinked records from `tbl_file_upload`.

```php
<?php

namespace common\models;

class Order extends \yii\db\ActiveRecord
{
    public function afterDelete()
    {
        parent::afterDelete();

        \common\models\FileUpload::deleteNotLinked();
    }
```
