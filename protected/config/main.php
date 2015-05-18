<?php

return [
    'basePath'            => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'language'            => 'ru',
    'name'                => 'Test application',
    'controllerNamespace' => 'app\\controllers',
    'preload'             => ['log'],
    'aliases'             => [
        'vendor'    => 'webroot.vendor',
        'bower'     => 'webroot.bower_components',
        'bootstrap' => 'vendor.drmabuse.yii-bootstrap-3-module',
        'xupload'   => 'vendor.asgaroth.xupload'
    ],
    'import'              => [
        'bootstrap.*',
        'bootstrap.components.*',
        'bootstrap.behaviors.*',
        'bootstrap.helpers.*',
        'bootstrap.widgets.*'
    ],
    'modules'             => [
        'import' => [
            'class' => 'app\\modules\\import\\ImportModule'
        ]
    ],
    'components'          => [
        'user'         => [
            'allowAutoLogin' => true,
        ],
        'urlManager'   => [
            'urlFormat' => 'path',
            'rules'     => [
                '<module:\w+>/<action:\w+>'                  => '<module>/index/<action>',
                '<controller:\w+>/<action:\w+>/<id:\d+>'     => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>'              => '<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
            ],
        ],
        'db'           => [
            'connectionString' => 'sqlite:' . dirname(__FILE__) . '/../data/main.db',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log'          => [
            'class'  => 'CLogRouter',
            'routes' => [
                [
                    'class' => 'CFileLogRoute'
                ]
            ],
        ],
        'bootstrap'    => [
            'class' => 'bootstrap.components.BsApi'
        ]
    ]
];