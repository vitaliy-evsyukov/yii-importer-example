<?php
/**
 * @var CClientScript $cs
 */
$cs = \Yii::app()->clientScript;
$am = \Yii::app()->assetManager;

$path = $am->publish(
   \Yii::getPathOfAlias('import.assets')
);

$cs->registerCssFile($path . '/css/style.css');
$cs->registerScriptFile($path . '/js/UploadHandler.js');
$this->beginContent('//layouts/main');
echo $content;
$this->endContent('//layouts/main');
?>