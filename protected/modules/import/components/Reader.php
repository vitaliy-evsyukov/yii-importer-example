<?php

namespace app\modules\import\components;

use app\modules\import\models\Transaction;

/**
 * Reader
 *
 * @author  Виталий Евсюков
 * @package app\modules\import\components
 */
class Reader
{
    public function load($fileName, Transaction $model)
    {
        $pReader = \PHPExcel_IOFactory::createReaderForFile($fileName);
        $pExcel  = $pReader->load($fileName);
        $sheet   = $pExcel->getSheet(0);
        $maxRow  = $sheet->getHighestRow();
        $minCol  = 'A';
        $maxCol  = $sheet->getHighestColumn();
        $header  = false;
        $result  = [];
        $fields  = [
            'date',
            'type',
            'payer',
            'recipient',
            'currency',
            'amount',
            'note'
        ];
        for ($row = 1; $row <= $maxRow; $row++) {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray(
                sprintf('%s%d:%s%d', $minCol, $row, $maxCol, $row),
                null,
                true,
                false
            );
            if (isset($rowData[0])) {
                $rowData = $rowData[0];
                if (!$header) {
                    if ($index = array_search('DateID', $rowData, true)) {
                        $header = true;
                        $minCol = \PHPExcel_Cell::stringFromColumnIndex($index + 1);
                        $maxCol = \PHPExcel_Cell::stringFromColumnIndex($index + sizeof($fields));
                    }
                } else {
                    if (is_null($rowData[0])) {
                        break;
                    }
                    $rowData[0] = gmdate('c', \PHPExcel_Shared_Date::ExcelToPHP($rowData[0]));
                    $values     = [];
                    foreach ($fields as $index => $field) {
                        $values[$field] = $rowData[$index];
                    }
                    $values['amount'] *= 100;
                    $values['status'] = Transaction::IMPORTING_STATUS;
                    $result[]         = $values;
                }
            }
        }
        if (empty($result)) {
            throw new \app\modules\import\components\exceptions\Transaction(405, 'Файл пуст');
        }
        $transaction = $model->getDbConnection()->beginTransaction();
        $imported    = ['old' => 0, 'new' => 0];
        try {
            $model->deleteAll('status = :name', [':name' => Transaction::IMPORTING_STATUS]);
            foreach ($result as $values) {
                $model->setAttributes($values);
                $model->id          = null;
                $model->hash        = null;
                $model->isNewRecord = true;
                $model->save();
                $errors = $model->getErrors();
                if (!empty($errors)) {
                    if ($model->getErrors('hash')) {
                        ++$imported['old'];
                    } else {
                        $errors = current($errors);
                        $errors = current($errors);
                        throw new \app\modules\import\components\exceptions\Transaction(405, $errors);
                    }
                } else {
                    ++$imported['new'];
                }
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw $e;
        }
        return $imported;
    }
}