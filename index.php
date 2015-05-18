<?php
$yii    = dirname(__FILE__) . '/vendor/yiisoft/yii/framework/yii.php';
$loader = dirname(__FILE__) . '/vendor/autoload.php';
$config = dirname(__FILE__) . '/protected/config/main.php';

defined('YII_DEBUG') or define('YII_DEBUG', false);

require_once($yii);
/**
 * @var \Composer\Autoload\ClassLoader $loader
 */
$loader = require($loader);
$loader->addPsr4('app\\', 'protected');

Yii::createWebApplication($config)->run();



