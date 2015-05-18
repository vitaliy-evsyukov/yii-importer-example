<?php
$this->widget(
    'bootstrap.widgets.BsGridView',
    array(
        'id'           => 'importing-grid',
        'dataProvider' => $model->search(),
        'columns'      => array(
            'transactionDate',
            'type',
            'payer',
            'recipient',
            'currency',
            [
                'name'  => 'transactionAmount',
                'value' => function ($data) {
                    return number_format($data->transactionAmount, 2, ',', ' ');
                }
            ],
            'note'
        ),
        'type'         => BsHtml::GRID_TYPE_STRIPED,
    )
);
?>