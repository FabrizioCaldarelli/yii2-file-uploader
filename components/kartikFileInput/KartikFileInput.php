<?php
namespace sfmobile\ext\fileUploader\components\kartikFileInput;

use yii\base\Widget;

/**
* Kartik File Input Widget Wrapper
* @package sfmobile\ext\fileUploader\components\kartikFileInput
* @version 1.0.1
*/
class KartikFileInput extends Widget {

    public $model;
    public $modelName;
    public $attributeName;
    public $acceptedTypes = 'image/*';
    public $maxFileCount = 1;

    /**
    * @var auto detect file preview type
    * @since 1.0.1
    */
    public $detectPreviewType = true;

    public function init(){
        parent::init();
    }

    public function run(){

        return $this->render('kartikFileInput', [
            'model' => $this->model,
            'modelName' => $this->modelName,
            'attributeName' => $this->attributeName,
            'acceptedTypes' => $this->acceptedTypes,
            'maxFileCount' => $this->maxFileCount,
            'detectPreviewType' => $this->detectPreviewType,
        ]);
    }
}
?>
