<?php

namespace app\modules\import\models;

/**
 * Transaction
 *
 * @author  Виталий Евсюков
 * @package app\modules\import\models
 */

class Transaction extends \CActiveRecord
{
    const EXISTING_STATUS = 'existing';

    const IMPORTING_STATUS = 'importing';

    public $transactionDate;

    public $transactionAmount;

    private $aliases = [
        'transactionDate'   => ['view' => "strftime('%d.%m.%Y', date)", 'sort' => 'date'],
        'transactionAmount' => ['view' => '(amount * 1.0 / 100)', 'sort' => 'amount']
    ];

    private $criteriaStatus = self::EXISTING_STATUS;

    public function tableName()
    {
        return 'transactions';
    }

    protected function query($criteria, $all = false)
    {
        return parent::query($criteria, $all);
    }

    public function attributeNames()
    {
        $aliases    = [];
        $directions = ['asc', 'desc'];
        foreach ($this->aliases as $alias => $replacement) {
            foreach ($directions as $direction) {
                $aliases[$alias][$direction] = sprintf('%s %s', $replacement['sort'], strtoupper($direction));
            }
        }
        return array_merge(
            parent::attributeNames(),
            $aliases
        );
    }

    public function attributeLabels()
    {
        return [
            'transactionDate'   => 'Дата',
            'type'              => 'Тип',
            'payer'             => 'Отправитель',
            'recipient'         => 'Получатель',
            'currency'          => 'Валюта',
            'transactionAmount' => 'Сумма',
            'note'              => 'Примечание'
        ];
    }

    public function setStatus($status)
    {
        if (!in_array($status, [self::EXISTING_STATUS, self::IMPORTING_STATUS])) {
            throw new \app\modules\import\components\exceptions\Transaction(
                405,
                'Отображать можно либо существующие, либо записи для импорта'
            );
        }
        $this->criteriaStatus = $status;
    }

    public function search()
    {
        $criteria = new \CDbCriteria(
            [
                'select'    => $this->replaceAliases(
                    ['id', 'transactionDate', 'payer', 'recipient', 'currency', 'transactionAmount', 'note', 'type'],
                    true
                ),
                'condition' => 'status = :name',
                'params'    => [':name' => $this->criteriaStatus]
            ]
        );

        return new \CActiveDataProvider(
            get_class($this),
            [
                'criteria'   => $criteria,
                'pagination' => ['pageSize' => 10],
                'sort'       => ['defaultOrder' => ['transactionDate' => \CSort::SORT_DESC]]
            ]
        );
    }

    private function replaceAliases($data, $concat, $aliases = [])
    {
        if (is_array($data)) {
            foreach ($data as &$element) {
                if (isset($this->aliases[$element])) {
                    $element = $this->replaceAliases($element, $concat, [$element => $this->aliases[$element]]);
                }
            }
        } else {
            if (!$aliases) {
                $aliases = $this->aliases;
            }
            foreach ($aliases as $alias => $replacement) {
                $replacement = $concat ? sprintf('%s AS %s', $replacement['view'], $alias) : $replacement['view'];
                $data        = str_replace($alias, $replacement, $data);
            }
        }
        return $data;
    }

    protected function beforeValidate()
    {
        $data = $this->getAttributes();
        unset($data['status']);
        $hash = hash('sha512', serialize($data));
        $this->hash = $hash;
        return parent::beforeValidate();
    }

    public function rules()
    {
        $re    = '/^[\p{Cyrillic}\p{Pd}0-9a-zA-Z\s[:punct:]\№\«\»]+$/u';
        $rules = [
            ['date', 'date', 'format' => 'yyyy-MM-ddTHH:mm:ss+00:00'],
            ['type', 'in', 'range' => ['Deposit', 'Withdrawal']],
            ['payer, recipient, note', 'match', 'pattern' => $re],
            ['currency', 'in', 'range' => ['USD', 'RUB', 'EUR']],
            ['amount', 'numerical', 'min' => 1, 'integerOnly' => true],
            ['status', 'in', 'range' => [self::IMPORTING_STATUS, self::EXISTING_STATUS]],
            ['hash', 'unique'],
            ['hash', 'match', 'pattern' => '/^[[:alnum:]]+$/']
        ];
        foreach ($rules as &$rule) {
            $rule['allowEmpty'] = false;
        }
        return $rules;
    }
}