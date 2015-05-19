<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->pageTitle ?></title>
    <?php
    $cs = \Yii::app()->clientScript;
    $am = \Yii::app()->assetManager;

    $path = $am->publish(
        \Yii::getPathOfAlias('bower') . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'dist/'
    );

    $cs->registerCssFile($path . '/css/bootstrap.css');
    $cs->registerCssFile($path . '/css/bootstrap-theme.css');

    /**
     * JavaScripts
     */
    $cs->registerCoreScript('jquery', CClientScript::POS_END);
    $cs->registerCoreScript('jquery.ui', CClientScript::POS_END);
    $cs->registerScriptFile($path . '/js/bootstrap.min.js', CClientScript::POS_END);
    $cs->registerScript(
        'tooltip',
        "$('[data-toggle=\"tooltip\"]').tooltip();$('[data-toggle=\"popover\"]').tooltip()",
        CClientScript::POS_READY
    );
    ?>
</head>
<body>

<div id="wrap">
    <div class="container"><?php echo $content; ?></div>
</div>
<div class="clearfix"></div>
</body>
</html>