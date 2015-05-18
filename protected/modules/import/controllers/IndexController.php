<?php

namespace app\modules\import\controllers;

use app\modules\import\models\Transaction;

/**
 * IndexController
 *
 * @author  Виталий Евсюков
 * @package app\modules\import\controllers
 */
class IndexController extends \CController
{
    public function actionIndex()
    {
        $model = $this->getTransaction();
        $new   = $model->count('status = :name', [':name' => Transaction::IMPORTING_STATUS]);
        $data  = ['model' => $model];
        if (\Yii::app()->request->isAjaxRequest) {
            $method = 'renderPartial';
            $view   = '_grid';
        } else {
            $method = 'render';
            $view   = 'index';
            \Yii::import('xupload.models.XUploadForm');
            $data['upload'] = new \XUploadForm();
            if (\Yii::app()->user->hasState('imported')) {
                $data['imported'] = [
                    'new' => $new,
                    'old' => $model->count() - $new
                ];
            }
        }
        if (!$new) {
            $model->setStatus(Transaction::EXISTING_STATUS);
            \Yii::app()->user->clearStates();
        }
        $this->$method($view, $data);
    }

    public function actionGetImporting()
    {
        $imported = \Yii::app()->user->getState('imported');
        if (isset($imported['result']['new'])) {
            $new = $imported['result']['new'];
        } else {
            $new = 0;
        }
        $this->renderPartial('_status', ['new' => $new], false, true);
    }

    /**
     * @return Transaction
     * @throws \CHttpException
     */
    private function getTransaction()
    {
        $imported    = \Yii::app()->user->getState('imported');
        $transaction = new Transaction();
        if (!empty($imported)) {
            $transaction->setStatus(Transaction::IMPORTING_STATUS);
        }
        return $transaction;
    }
}