<?php
namespace sfmobile\ext\fileUploader\components\kartikFileInput;

use yii\widgets\InputWidget;

/**
* Kartik File Input Widget Wrapper
* @package sfmobile\ext\fileUploader\components\kartikFileInput
* @version 1.0.1
*/
class KartikFileInput extends InputWidget {

    /**
    * @var accepted file types for upload
    * @since 1.0.0
    */
    public $acceptedTypes = 'image/*';

    /**
    * @var max files to uploader. Default (null) is infinite
    * @since 1.0.0
    */
    public $maxFileCount = null;

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
            'attribute' => $this->attribute,
            'acceptedTypes' => $this->acceptedTypes,
            'maxFileCount' => $this->maxFileCount,
            'detectPreviewType' => $this->detectPreviewType,
        ]);
    }
}
?>
