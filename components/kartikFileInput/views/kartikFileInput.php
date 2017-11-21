<?php     

$moduleId = (\sfmobile\ext\fileUploader\Module::getInstance()->id);

$initialPreview = [];
$initialPreviewConfig = [];
$filesInSession = \sfmobile\ext\fileUploader\models\FileInSession::listItems($modelName, $attributeName);
foreach($filesInSession as $fis)
{
    $initialPreview[] = \yii\helpers\Url::to([$moduleId.'/file-in-session/get', 'model' => $fis->modelName, 'attr' => $fis->attributeName, 'name' => $fis->fileName], true);
    $initialPreviewConfig[] = [
        'caption' => $fis->fileName, 
        'size' => $fis->fileSize,
        'url' => \yii\helpers\Url::to([$moduleId.'/file-in-session/delete', 'model' => $fis->modelName, 'attr' => $fis->attributeName, 'name' => $fis->fileName], true)
   ];
}
?>
<?php
echo \kartik\file\FileInput::widget([
    'model' => $model,
    'attribute' => $attributeName.'[]',
    'options' => [
        'accept' => $acceptedTypes,
        'multiple' => true,
    ],
    'pluginOptions' => [
        'maxFileCount' => $maxFileCount,
    
        'previewFileType' => 'any',
        'showPreview' => true,
        'showCaption' => true,
        'showRemove' => true,
        'showUpload' => false,
        'overwriteInitial' => false,
        
        'initialPreview'=> $initialPreview,
        'initialPreviewAsData'=>true,
        'initialPreviewConfig' => $initialPreviewConfig,
        
    ],        
]);   
?> 