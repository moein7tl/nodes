<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');
Yii::setAlias('tests', __DIR__ . '/../tests');
$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

return [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'gii' => 'yii\gii\Module',
        'user' => [
            'class' => 'app\modules\user\Module',
        ],        
        'post'  =>  [
            'class' => 'app\modules\post\Module',
        ],
        'embed' =>  [
            'class' => 'app\modules\embed\Module',
        ],
        'social' => [
            'class' => 'app\modules\social\Module',
        ],        
        'daemon' => [
            'class' => 'app\modules\daemon\Module',
        ],        
    ],
    'components' => [
        'ftpFs' => [
            'class' => 'creocoder\flysystem\FtpFilesystem',
            'host'  => 'b101054.parspack.org',
            'port'  => 21,
            'username' => 'b101054',
            'password' => 'DdIlxJvBmSp2yM',
            // 'root' => '/path/to/root',
            // 'passive' => false,
            // 'ssl' => true,
            // 'timeout' => 60,
            // 'permPrivate' => 0700,
            // 'permPublic' => 0744,
            // 'transferMode' => FTP_TEXT,
        ],                   
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 30 : 0,
            'targets' => [
                [
                    'class'         => 'yii\log\FileTarget',
                    'logFile'       => '@app/runtime/logs/app.log',                    
                    'levels'        => 0,//0 means all
                    'categories'    => [],                    
                ],
            ],
        ],
        'urlManager'                =>  [
//            'showScriptName'    => false,
//            'enablePrettyUrl'   => true,
                'rules' => [
                    'social-daemon/index'   =>  'daemon/social-daemon/index'
                ]
        ],
        'socialClientCollection'    =>  require(__DIR__ . '/social.php'),
        'db' => $db,
        'gearman' => [
            'class' => 'filsh\yii2\gearman\GearmanComponent',
            'servers' => [
                ['host' => '127.0.0.1', 'port' => 4730],
            ],
            'user' => 'moein7tl',
            'jobs' => [
                'syncImage' =>  [
                    'class' => 'app\gearworker\SyncImage'
                ],
            ]
        ],           
    ],
    'controllerMap' => [
        'gearman' => [
            'class'             => 'filsh\yii2\gearman\GearmanController',
            'gearmanComponent'  => 'gearman'
        ],
        'fixture' => [
            'class'             => 'yii\faker\FixtureController',
            'templatePath'      => 'tests/unit/fixtures',
        ],
        'social-daemon' =>  [
            'class'             => 'app\modules\daemon\controllers\SocialDaemonController',
        ]
    ],    
    'aliases' => [
        '@temp'         =>  '@app/temp',
        '@pictures'     =>  '@temp/pictures',
        '@covers'       =>  '@temp/covers',        
        '@ftp'          =>  '/assets',
//        '@tests'        =>   __DIR__ . '/../tests',
    ], 
    'params' => $params,
];
