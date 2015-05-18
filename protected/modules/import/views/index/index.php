<?php
$this->pageTitle = 'Транзакции';
$force           = 'false';
if (isset($imported)) {
    $new   = $imported['new'];
    $old   = $imported['old'];
    $force = 'true';
} else {
    $new = 0;
    $old = 0;
}
?>
    <h3>Транзакции</h3>
    <div class="b-summary b-summary--badges">
        <span class="b-summary__item">Новых</span>
        <span class="b-summary__item b-summary__item--counter"><?php echo BsHtml::badge(0) ?></span>
        <span class="b-summary__item">Уже добавленных</span>
        <span class="b-summary__item b-summary__item--counter"><?php echo BsHtml::badge(0) ?></span>
    </div>
    <div class="b-summary b-summary--result">
        <?php $this->renderPartial('_status', ['new' => $new]); ?>
    </div>
<?php $this->renderPartial('_grid', ['model' => $model]); ?>
    <div class="b-confirm pull-right"><?php echo BsHtml::ajaxButton(
            'Подтвердить ' . BsHtml::badge(0),
            '/import/file/confirm',
            [
                'beforeSend' => 'function() { UploadHandler.fetchInfo(); }',
                'success'    => 'function() { var u = UploadHandler; u.changeBadges(0, 0); u.showInfo(); }',
                'error'      => 'function(data) { alert(data.responseText) }'
            ]
        ); ?></div>
    <fieldset>
        <?php
        $form = $this->beginWidget(
            'CActiveForm',
            array(
                'id'                   => 'import-form',
                'enableAjaxValidation' => false,
                'htmlOptions'          => ['enctype' => 'multipart/form-data'],
            )
        );
        ?>
        <div class="row">
            <div class="col-md-1">
                <h3><?php echo BsHtml::labelBs('Загрузка файлов', ['color' => BsHtml::LABEL_COLOR_INFO]); ?></h3>
            </div>
        </div>
        <div class="col-md-1">
            <?php
            $this->widget(
                '\\app\\modules\\import\\components\\FixedUpload',
                [
                    'url'         => '/import/file/upload',
                    'model'       => $upload,
                    'htmlOptions' => ['id' => 'import-form'],
                    'autoUpload'  => true,
                    'attribute'   => 'file',
                    'multiple'    => false,
                    'options'     => [
                        'acceptFileTypes' => "js:/(\.|\/)xlsx$/i",
                        'completed'       => 'js:UploadHandler.processUpload.bind(UploadHandler)'
                    ],
                    'showForm'    => false,
                ]
            );
            ?>
        </div>
        <div class="clearfix"></div>
        <?php $this->endWidget(); ?>
    </fieldset>
<?php
/**
 * @var CClientScript $cs
 */
$cs = \Yii::app()->clientScript;
$cs->registerScript('upHandler', "UploadHandler.changeBadges($new, $old, $force);", CClientScript::POS_READY);
?>