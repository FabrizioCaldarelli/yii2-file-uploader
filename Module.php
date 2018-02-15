<?php

/**
 * @copyright Copyright &copy; Fabrizio Caldarelli, sfmobile.it, 2017
 * @package yii2-file-uploader
 * @version 1.0.1
 */

namespace sfmobile\ext\fileUploader;

/**
 * Yii2FileUploader module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * Base url for images
     */
    public $imagesBaseUrl;

    /**
     * Base path for file uploaded
     */
    public $fileUploadBasePath;

    /**
     * Base url for file uploaded
     */
    public $fileUploadBaseUrl;

    /**
     * Db table name
     * @since 1.0.1
     */
    public $dbTableName = 'file_upload';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'sfmobile\ext\fileUploader\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
