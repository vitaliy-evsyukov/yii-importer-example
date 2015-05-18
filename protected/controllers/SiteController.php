<?php

/**
 * SiteController
 *
 * @author Виталий Евсюков
 */

namespace app\controllers;

class SiteController extends \CController
{
    public function actionError()
    {
        $error = \Yii::app()->errorHandler->error;
        if ($error) {
            if (\Yii::app()->request->isAjaxRequest) {
                echo $error['message'];
            } else {
                header('Content-type: application/json');
                $this->renderPartial('error', ['error' => $error]);
            }
        }
    }
}