<?php
namespace sfmobile\ext\fileUploader\components\kartikFileInput;

use yii\base\Widget;

class KartikFileInput extends Widget {
    
    public $model;
    public $modelName;
    public $attributeName;
    public $acceptedTypes = 'image/*';
    public $maxFileCount = 1;
    
    public function init(){
        parent::init();
    }
    
    public function run(){
        
        return $this->render('kartikFileInput', ['model' => $this->model, 'modelName' => $this->modelName, 'attributeName' => $this->attributeName, 'acceptedTypes' => $this->acceptedTypes, 'maxFileCount' => $this->maxFileCount]);
    }
}
?>