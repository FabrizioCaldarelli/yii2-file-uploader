<?php

namespace sfmobile\ext\fileUploader\models;

use Yii;

/**
 * This is the model class for table "tbl_file_upload".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $section
 * @property string $category
 * @property integer $refer_id
 * @property string $file_name
 * @property string $file_name_original
 * @property string $description
 * @property string $mime_type
 * @property integer $file_size
 * @property string $approved
 * @property string $relative_path
 * @property string $refer_table
 * @property string $create_time
 * @version 1.0.3
 */
class FileUpload extends \yii\db\ActiveRecord
{
    public function afterDelete()
    {
        parent::afterDelete();

        // Cancella tutti i files relativi a quel filename (le varie versioni a risoluzioni diverse)
        $path_parts = pathinfo($this->absolutePathFile);
        $ppDirname = $path_parts['dirname'];
        $ppFilename =$path_parts['filename'];
        if(file_exists($ppDirname))
        {
            $arrFiles =scandir($ppDirname);
            foreach($arrFiles as $f)
            {
                $pf = $ppDirname.'/'.$f;
                if(is_file($pf))
                {
                    if(strpos($f, $ppFilename) === 0)
                    {
                        @unlink($pf);
                    }
                }
            }
        }
    }

    /**
	 **************************************
	 * Sync data with and from database
	 **************************************
    */
    public static function syncDatabaseFromListFilesSession($lstFilesInSession, $section, $category, $userId, $referOptions)
    {
        $arrOut = [];

		$conditions = array_merge(['section' => $section, 'category' => $category], $referOptions);

        $lstFilesUpload = self::find()->where($conditions)->all();

        foreach($lstFilesInSession as $fis)
        {
            $fu = new self();
            $fu->section = $section;
            $fu->category = $category;
            $fu->user_id = $userId;
            $fu->file_name = sha1(basename($fis->fileName)).'.'.strtolower(pathinfo( $fis->fileName, PATHINFO_EXTENSION));
            $fu->file_name_original = $fis->fileName;
            $fu->mime_type = $fis->fileMimeType;
            $fu->file_size = $fis->fileSize;
            $fu->refer_table = $section;
            $fu->approved = 'yes';
            $fu->create_time = date('Y-m-d H:i:s');

			// Fill refer_id and other refers
			foreach($referOptions as $rk=>$rv)
			{
				$fu->setAttribute($rk, $rv);
			}

            $fu->relative_path = $fu->relativePathFromDbRecord();

            // Controlla se il file già esiste
            $keyTrovatoTestFU = -1;
            $arrKeysFilesUpload = array_keys($lstFilesUpload);
            for($k=0;$k<count($lstFilesUpload);$k++)
            {
                $tempKey = $arrKeysFilesUpload[$k];
                $testFU = $lstFilesUpload[ $tempKey ];
                if(($testFU->file_name_original == $fu->file_name_original)&&($testFU->file_size == $fu->file_size)) $keyTrovatoTestFU = $tempKey;
            }

            // Il file esiste già
            if($keyTrovatoTestFU!=-1)
            {
                $arrOut[] = $testFU;
                unset($lstFilesUpload[$keyTrovatoTestFU]);
            }
            else {

                $retSave = $fu->save();

                if($retSave)
                {
                    $fuPathFile = $fu->absolutePathFile;

                    $basepath = dirname($fuPathFile);
                    if(file_exists($basepath) == false) mkdir($basepath, 0777, true);

                    file_put_contents($fuPathFile, $fis->data);

                    $arrOut[] = $fu;
                }
            }

        }

        // Elimina gli ultimi file rimasti precedentemente
        foreach($lstFilesUpload as $fu)
        {
            $fu->delete();
        }

        return $arrOut;
    }

    public static function syncFilesFromSessiondAndRemoveFromSession($modelName, $attributeName, $section, $category, $userId, $referOptions)
    {
        $lstFileInSession = FileInSession::listItems($modelName, $attributeName);
        self::syncDatabaseFromListFilesSession($lstFileInSession, $section, $category, $userId, $referOptions);
        FileInSession::deleteListItems($modelName, $attributeName);
    }

    /**
	 **************************************
	 * Path and Url
	 **************************************
    */
    public function relativePathFromDbRecord()
    {
        //if(($fu->section == 'sec')&&($fu->category == 'cat')) $rel = self::relativePath_recordDb($fu);

        $rel = sprintf('/%s/%s/%d/%s', $this->section, $this->category, $this->refer_id, $this->file_name);

        return $rel;
    }

    public function getRelativePathFile()
    {
        return $this->relativePathFromDbRecord();
    }

    public function getAbsolutePathFile()
    {
        $out = null;
        $rel = $this->relativePathFile;

        if($rel != null)
        {
            $basePath = \sfmobile\ext\fileUploader\Module::getInstance()->fileUploadBasePath;
            $out = $basePath.$rel;
        }
        return $out;
    }

    /**
    * Return full file url, absolute or relative, based on isRequiredAbsolute parameter
    * @param $isAbsoluteUrl boolean Specify if it needed baseUrl
    */
    public function getUrlFile($options=null, $isAbsoluteUrl=false)
    {
        $out = null;
        $rel = $this->relativePathFromDbRecord();

        if($rel != null)
        {
            $baseUrl = \sfmobile\ext\fileUploader\Module::getInstance()->fileUploadBaseUrl;
            $out = $baseUrl.$rel;

            // If it is requested an absolute url, it checks that fileUploadbaseUrl is already in absolute form.
            // If it is already absolute, it does nothing, otherwise apply baseUrl.
            if($isAbsoluteUrl)
            {
                if(\sfmobile\ext\fileUploader\Module::getInstance()->isFileUploadBaseUrlAbsolute)
                {
                    // do nothing because fileUploadBaseUrl is already absolute
                }
                else
                {
                    // it apply host base url
                    $out = \yii\helpers\Url::to($out, true);
                }
            }

        }
        return $out;
    }



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return \sfmobile\ext\fileUploader\Module::getInstance()->dbTableName;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'section', 'category', 'refer_id', 'file_name', 'file_name_original',  'mime_type', 'file_size', 'approved', 'relative_path', 'refer_table'], 'required'],
            [['user_id', 'refer_id', 'file_size'], 'integer'],
            [['approved'], 'string'],
            [['create_time'], 'safe'],
            [['section', 'category', 'file_name', 'file_name_original', 'refer_table'], 'string', 'max' => 150],
            [['description'], 'string', 'max' => 500],
            [['mime_type'], 'string', 'max' => 100],
            [['relative_path'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'section' => Yii::t('app', 'Section'),
            'category' => Yii::t('app', 'Category'),
            'refer_id' => Yii::t('app', 'Refer ID'),
            'file_name' => Yii::t('app', 'File Name'),
            'file_name_original' => Yii::t('app', 'File Name Original'),
            'description' => Yii::t('app', 'Description'),
            'mime_type' => Yii::t('app', 'Mime Type'),
            'file_size' => Yii::t('app', 'File Size'),
            'approved' => Yii::t('app', 'Approved'),
            'relative_path' => Yii::t('app', 'Relative Path'),
            'refer_table' => Yii::t('app', 'Refer Table'),
            'create_time' => Yii::t('app', 'Create Time'),
        ];
    }

    /**
     * @inheritdoc
     * @return \backend\modules\yii2_file_uploader\models\query\FileUploadQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \sfmobile\ext\fileUploader\models\query\FileUploadQuery(get_called_class());
    }
}
