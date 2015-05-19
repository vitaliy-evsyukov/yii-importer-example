<?php

namespace app\modules\import;

/**
 * ImportModule
 *
 * @author  Виталий Евсюков
 * @package app\modules\import
 */

class ImportModule extends \CWebModule
{
    public $defaultController = 'index';

    public $controllerNamespace = 'app\\modules\\import\\controllers';

    protected function init()
    {
        parent::init();
        $this->setImport(['xupload.XUpload', 'xupload.models.XUploadForm']);
        $this->layout = 'main';
    }
}