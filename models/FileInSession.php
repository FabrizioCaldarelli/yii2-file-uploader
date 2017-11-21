<?php

namespace sfmobile\ext\fileUploader\models;

use Yii;

class FileInSession extends \yii\base\Model 
{
    public $formInputInfo;
    public $fileUploadAttributes;

    public $modelName;
    public $attributeName;
    public $data;
    
	public function getFileName()
	{
		$retVal = null;
		if(($this->formInputInfo!=null)&&(isset($this->formInputInfo['name']))&&($this->formInputInfo['name']!=''))
		{
			$retVal = $this->formInputInfo['name'];
		}
		else if(($this->fileUploadAttributes!=null)&&(isset($this->fileUploadAttributes['file_name_original']))&&($this->fileUploadAttributes['file_name_original']!=null))
		{
			$retVal = $this->fileUploadAttributes['file_name_original'];
		}
		return $retVal;
	}
	
	public function getFileSize()
	{
		$retVal = null;
		if(($this->formInputInfo!=null)&&(isset($this->formInputInfo['size']))&&($this->formInputInfo['size']!=''))
		{
			$retVal = $this->formInputInfo['size'];
		}
		else if(($this->fileUploadAttributes!=null)&&(isset($this->fileUploadAttributes['file_size']))&&($this->fileUploadAttributes['file_size']!=null))
		{
			$retVal = $this->fileUploadAttributes['file_size'];
		}
		return $retVal;
	}	
	
	public function getFileMimeType()
	{
		$retVal = null;
		if(($this->formInputInfo!=null)&&(isset($this->formInputInfo['type']))&&($this->formInputInfo['type']!=''))
		{
			$retVal = $this->formInputInfo['type'];
		}
		else if(($this->fileUploadAttributes!=null)&&(isset($this->fileUploadAttributes['mime_type']))&&($this->fileUploadAttributes['mime_type']!=null))
		{
			$retVal = $this->fileUploadAttributes['mime_type'];
		}
		return $retVal;
	}
	        
    public static function createListFromModel($lstFilesUpload, $modelName, $attributeName)
    {
        $arrObj = [];
        
        foreach($lstFilesUpload as $fu)
        {
            if(file_exists($fu->absolutePathFile) == false) continue;
            
            $data = file_get_contents($fu->absolutePathFile);
            
            $fis = new self();
            $fis->modelName = $modelName;
            $fis->attributeName = $attributeName;
            $fis->fileUploadAttributes = $fu->attributes;
			$fis->data = $data;
            
            $arrObj[$fu->file_name_original] = $fis;
        }
        
        // Salva in sessione
        $key = $modelName.'.'.$attributeName;
        $session = \Yii::$app->session;
        $session->set($key, $arrObj);        
        
        return $arrObj;
    }    
    
    public static function createListFromForm($modelName)
    {
        $arrOut = [];
        if(isset($_FILES[$modelName]))
        {
            $lstFuName = $_FILES[$modelName]['name'];
            $lstFuType = $_FILES[$modelName]['type'];
            $lstFuTmpName = $_FILES[$modelName]['tmp_name'];
            $lstFuError = $_FILES[$modelName]['error'];
            $lstFuSize = $_FILES[$modelName]['size'];
            for($k=0;$k<count($lstFuTmpName);$k++)
            {
                foreach($lstFuTmpName as $attributeName=>$temp)
                {
                    if(is_array($temp) == false) continue;
                    
                    for($j=0;$j<count($lstFuTmpName[$attributeName]);$j++)
                    {
                        $fuName = $lstFuName[$attributeName][$j];
                        $fuType = $lstFuType[$attributeName][$j];
                        $fuError = $lstFuError[$attributeName][$j];
                        $fuTmpName = $lstFuTmpName[$attributeName][$j];
                        $fuSize = $lstFuSize[$attributeName][$j];
                        if(($fuError == 0)&&($fuSize>0)&&file_exists($fuTmpName))
                        {
                            $formInputInfo = [ 'name' => $fuName, 'type' => $fuType, 'error' => $fuError, 'tmpName' => $fuTmpName, 'size' => $fuSize ];
                            
                            $fuData = file_get_contents($fuTmpName);
                            $fileInSession = self::create($modelName, $attributeName, $formInputInfo, null, $fuData);
                            
                            $arrOut[] = $fileInSession;
                            
                        }
                   }
                }
            }
         }

        return $arrOut;  
    } 

    public static function initFromModelOrCreateFromForm($modelName, $attributeName, $lstModelFiles)
    {
        $arrFilesInSessionPerAttributeName = FileInSession::createListFromForm($modelName);
        
        if((count($arrFilesInSessionPerAttributeName) == 0)&&(isset($_POST[$modelName]) == false))
        {
            // Inizializza i files
            FileInSession::createListFromModel($lstModelFiles, $modelName, $attributeName);
        }        
    }
    
    public static function create($modelName, $attributeName, $formInputInfo, $fileUploadAttributes, $data)
    {
        $obj = new self();
        $obj->modelName = $modelName;
        $obj->attributeName = $attributeName;
        $obj->formInputInfo = $formInputInfo;
        $obj->fileUploadAttributes = $fileUploadAttributes;
        $obj->data = $data;
        
        $filename = $formInputInfo['name'];

        // Salva in sessione
        $key = $modelName.'.'.$attributeName;
        $session = \Yii::$app->session;
        $arrObj = [];
        if($session->has($key)) $arrObj = $session->get($key);
        $arrObj[$filename] = $obj;
        $session->set($key, $arrObj);
        
        
        return $obj;
    }
    
    public static function listItems($modelName, $attributeName)
    {
        // Recupera dalla sessione
        $key = $modelName.'.'.$attributeName;
        $session = \Yii::$app->session;
        $arrObj = [];
        if($session->has($key)) $arrObj = $session->get($key);
        return $arrObj;        
    }
    
    public static function getItem($modelName, $attributeName, $filename)
    {
        // Recupera dalla sessione
        $key = $modelName.'.'.$attributeName;
        $session = \Yii::$app->session;
        $arrObj = [];
        if($session->has($key)) $arrObj = $session->get($key);
        
        $obj = (isset($arrObj[$filename]))?$arrObj[$filename]:null;
        
        return $obj;
    }
    
    public static function deleteListItems($modelName, $attributeName)
    {
        $key = $modelName.'.'.$attributeName;
        $session = \Yii::$app->session;
        $session->remove($key);
    }    

    public static function deleteItem($modelName, $attributeName, $filename)
    {
        $key = $modelName.'.'.$attributeName;
        $session = \Yii::$app->session;
        $arrObj = [];
        if($session->has($key)) $arrObj = $session->get($key);
        
        $obj = (isset($arrObj[$filename]))?$arrObj[$filename]:null;
        
        if($obj!=null)
        {
            unset($arrObj[$filename]);
            $session->set($key, $arrObj);
        }
        
        return $obj;        
    }
    
}

?>
