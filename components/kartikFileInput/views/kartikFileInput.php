<?php

$modelName = \yii\helpers\StringHelper::basename(get_class($model));

$moduleId = (\sfmobile\ext\fileUploader\Module::getInstance()->id);

$initialPreview = [];
$initialPreviewConfig = [];
$filesInSession = \sfmobile\ext\fileUploader\models\FileInSession::listItems($modelName, $attribute);
foreach($filesInSession as $fis)
{
    $mimeType = $fis->fileMimeType;

    $initialPreview[] = \yii\helpers\Url::to([$moduleId.'/file-in-session/get', 'model' => $fis->modelName, 'attr' => $fis->attributeName, 'name' => $fis->fileName], true);

    $previewType = 'image';

    // If enabled detect preview type, use it
    if($detectPreviewType)
    {
        if(strpos($mimeType, 'image/') === 0) $previewType = 'image';
        if(strpos($mimeType, 'video/') === 0) $previewType = 'video';
        if(strpos($mimeType, 'audio/') === 0) $previewType = 'audio';
        if(strpos($mimeType, 'text/html') === 0) $previewType = 'html';
        if(strpos($mimeType, 'application/pdf') === 0) $previewType = 'pdf';
    }

    $initialPreviewConfig[] = [
        'type' => $previewType,
        'caption' => $fis->fileName,
        'size' => $fis->fileSize,
        'url' => \yii\helpers\Url::to([$moduleId.'/file-in-session/delete', 'model' => $fis->modelName, 'attr' => $fis->attributeName, 'name' => $fis->fileName], true)
   ];
}
?>
<?php
echo \kartik\file\FileInput::widget([
    'model' => $model,
    'attribute' => $attribute.'[]',
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
