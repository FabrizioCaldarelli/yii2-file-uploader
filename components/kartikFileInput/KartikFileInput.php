<?php
namespace sfmobile\ext\fileUploader\components\kartikFileInput;

use yii\widgets\InputWidget;

/**
* Kartik File Input Widget Wrapper
* @package sfmobile\ext\fileUploader\components\kartikFileInput
* @version 1.0.3
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

    /**
    * @var string define form model prefixSessionKey attribute name
    * @since 1.0.2
    */
    public $prefixSessionKeyAttribute = null;

    /**
    * @var string define validateInitialCount option
    * @since 1.0.3
    */
    public $validateInitialCount = true;

    /**
    * @var string define minFileCount option
    * @since 1.0.3
    */
    public $minFileCount = 0;

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
            'prefixSessionKeyAttribute' => $this->prefixSessionKeyAttribute,
            'validateInitialCount' => $this->validateInitialCount,
        ]);
    }
}
?>
