<?php

namespace sfmobile\ext\fileUploader\models\query;

/**
 * This is the ActiveQuery class for FileUpload class.
 *
 */
class FileUploadQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \backend\modules\yii2_file_uploader\models\FileUpload[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \backend\modules\yii2_file_uploader\models\FileUpload|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
