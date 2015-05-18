<?php

namespace app\modules\import\controllers;

use app\modules\import\components\Reader;
use app\modules\import\models\Transaction;

/**
 * FileController
 *
 * @author  Виталий Евсюков
 * @package app\modules\import\controllers
 */
class FileController extends \CController
{
    public function actionUpload()
    {
        $uploadForm = new \XUploadForm();
        $uploadPath = \Yii::getPathOfAlias('application.upload') . DIRECTORY_SEPARATOR;
        $accepting  = \CHttpRequest::parseAcceptHeader(\Yii::app()->request->getAcceptTypes());
        $headers    = ['Vary: Accept'];
        if (
            isset($accepting[0]) &&
            ($accepting[0]['type'] === 'application') &&
            ($accepting[0]['subType'] === 'json')
        ) {
            $headers[] = 'Content-type: application/json';
        } else {
            $headers[] = 'Content-type: text/plain';
        }
        $uploadForm->file = \CUploadedFile::getInstance($uploadForm, 'file');
        if ($uploadForm->file !== null) {
            $fields = ['mime_type' => 'getType', 'name' => 'getName', 'size' => 'getSize'];
            foreach ($fields as $field => $method) {
                $uploadForm->{$field} = $uploadForm->file->{$method}();
            }
            $extension = $uploadForm->file->getExtensionName();
            $filename  = md5($uploadForm->name . microtime() . rand());
            $savepath  = $uploadPath . $filename . '.' . $extension;
            $error     = false;
            if ($uploadForm->validate()) {
                $uploadForm->file->saveAs($savepath);
                chmod($savepath, 0775);
                try {
                    $reader = new Reader();
                    $import = $reader->load($savepath, new Transaction());
                    \Yii::app()->user->setState('imported', [$filename => $savepath, 'result' => $import]);
                    $this->renderJSON(
                        [
                            [
                                'name'        => $uploadForm->name,
                                'size'        => $uploadForm->size,
                                'type'        => $uploadForm->mime_type,
                                'delete_url'  => $this->createUrl('delete', ['filename' => $filename]),
                                'delete_type' => 'POST',
                                'imported'    => $import
                            ]
                        ]
                    );
                } catch (\app\modules\import\components\exceptions\Transaction $e) {
                    unlink($savepath);
                    $error = $e->getMessage();
                }
            } else {
                $error = $uploadForm->getErrors('file');
            }
            $this->renderJSON(['errors' => $error]);
        }
    }

    public function actionConfirm()
    {
        $imported = \Yii::app()->user->getState('imported');
        if (!empty($imported)) {
            $transactions = new Transaction();
            $transactions->updateAll(
                ['status' => Transaction::EXISTING_STATUS],
                'status = :name',
                [':name' => Transaction::IMPORTING_STATUS]
            );
            unlink(current($imported));
            $this->renderJSON(['success' => true]);
        } else {
            throw new \app\modules\import\components\exceptions\Transaction(405, 'Нечего подтверждать');
        }
    }

    public function actionDelete($filename)
    {
        $imported = \Yii::app()->user->getState('imported');
        if ($filename && isset($imported[$filename]) && is_file($imported[$filename])) {
            unlink($imported[$filename]);
        }
        \Yii::app()->user->clearStates();
        $this->renderJSON(['success' => true]);
    }

    private function renderJSON($data, $headers = ['Content-type: application/json'])
    {
        foreach ($headers as $header) {
            header($header);
        }
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        foreach (\Yii::app()->log->routes as $route) {
            if ($route instanceof \CWebLogRoute) {
                $route->enabled = false; // disable any weblogroutes
            }
        }
        \Yii::app()->end();
    }
}