<?php

namespace sfmobile\ext\fileUploader\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FileInSessionController
 */
class FileInSessionController extends Controller
{
    public function actionGet($model, $attr, $name, $psk = null)
    {
        $obj = \sfmobile\ext\fileUploader\models\FileInSession::getItem($model, $attr, $name, ['prefixSessionKey' => $psk] );

        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        \Yii::$app->response->headers->add('Content-Type', $obj->fileMimeType);
        \Yii::$app->response->data = $obj->data;
        \Yii::$app->response->send();
    }

    public function actionDelete($model, $attr, $name, $psk = null)
    {
        $obj = \sfmobile\ext\fileUploader\models\FileInSession::deleteItem($model, $attr, $name, ['prefixSessionKey' => $psk] );

        $out = ['action' => 'none'];

        if($obj!=null)
        {
            $out = [
                'action' => 'delete'
            ];
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $out;
    }

}
