<?php

$params = require(__DIR__ . '/params.php');
$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' =>  ['log'],
    'language'          =>  'fa-IR',
    'sourceLanguage'    =>  'en-US',
    'name'              =>  'Nodes Web',
    'components' => [
        'ftpFs' => require(__DIR__ . '/' . 'ftp.php'),        
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey'       => 'N9H_hIWERpGoYxMcgZnvFgmwsSVOck8j',
            'enableCsrfValidation'      =>  true,
            'enableCookieValidation'    =>  true,
        ],
        'redis' => [
            'class'     => 'yii\redis\Connection',
            'hostname'  => 'localhost',
            'port'      => 6379,
            'database'  => 0,
        ],
        'session' => [
            'class'     => 'yii\redis\Session',
        ],
        'cache' => [
            'class'     => 'yii\redis\Cache',
        ],          
        'user' => [
            'identityClass'     => 'app\modules\user\models\User',
            'enableAutoLogin'   => true,
            'loginUrl'          => ['','login'=>'required'],  
        ],
        'socialClientCollection'    =>  require(__DIR__ . '/social.php'),
        'authClientCollection'      => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'google' => [
                    'class'         => 'yii\authclient\clients\GoogleOAuth',
                    'clientId'      => '443205670238-vk10ehfmib8q9aljjad6dij0t1goam01.apps.googleusercontent.com',
                    'clientSecret'  => 'bn9q9cUf2IQP_rWpRacBbvJm',
                    'scope'         =>  implode(' ', ['email'])
                ],
                'github' => [
                    'class'         => 'yii\authclient\clients\GitHub',
                    'clientId'      => '1a1d4e30eab7d1bf87dd',
                    'clientSecret'  => 'e1dd76c0937a358ad51c55203360b222ea8c040a',
                    'scope'         =>  implode(' ', ['user:email'])
                ],                
                'linkedin' => [
                    'class'         => 'yii\authclient\clients\LinkedIn',
                    'clientId'      => '78pbzg810si8wb',
                    'clientSecret'  => 'TfZ4uwfiBgrb5V9w',
                ],                
                'facebook' => [
                    'class'         => 'yii\authclient\clients\Facebook',
                    'clientId'      => '865054846839734',
                    'clientSecret'  => '737f7715218838239a00a06aed234c13',
                ],
            ],
        ],        
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'view' => [
            'theme' => [
               'pathMap' => [
                    '@app/views'    =>  [
                        '@app/themes/seven/views'
                    ],
                    '@app/modules'  =>  [
                        '@app/themes/seven/views',
                    ],            
                ],
            ],
            'renderers' => [
                'twig' => [
                    'class' => 'yii\twig\ViewRenderer',
                    'cachePath' => '@runtime/Twig/cache',
                    // Array of twig options:
                    'options' => [
                        'auto_reload' => true,
                    ],
                    'globals' => ['html' => '\yii\helpers\Html'],
                    'uses' => ['yii\bootstrap'],
                ],
            ],            
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 30 : 0,
            'targets' => [
                [
                    'class'         => 'yii\log\FileTarget',
                    'logFile'       => '@app/runtime/logs/app.log',                    
                    'levels' => ['error', 'warning'],
                    'categories'    => [],                    
                ],
            ],
        ],
        'mailer'    => [
            'class'     =>  'nickcv\mandrill\Mailer',
            'apikey'    =>  '6mEUoQyuhDN4itn_O1UlCg',
            'htmlLayout'=>  '@app/themes/seven/views/user/views/mail/layout.php',
        ],
        'db'            => require(__DIR__ . '/db.php'),
        'urlManager'    => require(__DIR__ . '/url.php'),      
        'reCaptcha' => [
            'name'      =>  'reCaptcha',
            'class'     =>  'himiklab\yii2\recaptcha\ReCaptcha',
            'siteKey'   =>  '6LfK_gITAAAAAPC21IX_amSnxcR7d7be1aB9so48',
            'secret'    =>  '6LfK_gITAAAAAIqY0FMR1xbk990u2fVJ-BwCZuBn',
        ],
        'assetManager' => [
            'linkAssets'        => true,
            'appendTimestamp'   => true,
            'bundles'           => require(__DIR__ . '/' . (YII_ENV_PROD ? 'assets-prod.php' : 'assets-dev.php')),
        ],        
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'fileMap' => [
                        'app'   => 'app.php',
                    ],
                ],
            ],
        ]       
    ],
    'modules' => [
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
    ],    
    'aliases' => [
        '@runtimeTemp'              =>  '@app/runtime/temp/images',
        '@webTemp'                  =>  '@app/web/userassets',
        '@webTempPictures'          =>  '@webTemp/user/pictures',
        '@webTempCovers'            =>  '@webTemp/user/covers',
        '@webTempPostCover'         =>  '@webTemp/post/covers',
        '@webTempFolder'            =>  'userassets',
        '@webTempCoversFolder'      =>  '@webTempFolder/user/covers',
        '@webTempPicturesFolder'    =>  '@webTempFolder/user/pictures',
        '@webTempPostCoverFolder'   =>  '@webTempFolder/post/covers',
        '@ftpImages'                =>  '/post/images',
        
        '@ppicBaseUrl'      =>  'http://cdn.nodes.ir/user/pictures',
        '@cpicBaseUrl'      =>  'http://cdn.nodes.ir/user/covers',
        '@upBaseUrl'        =>  'http://cdn.nodes.ir/post/images',
        '@postCoverBaseUrl' =>  'http://cdn.nodes.ir/post/covers',
        '@gravatar'         =>  'http://www.gravatar.com/avatar',
        '@placeHold'        =>  'http://www.placehold.it',
        '@profile'          =>  'http://nodes.ir',
        
        
        '@uploaded'     =>  '@app/runtime/temp/images',
        '@temp'         =>  '@app/web/t',
        '@tempFolder'   =>  't',
        '@pictures'     =>  '@temp/pictures',
        '@covers'       =>  '@temp/covers',        

    ],    
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;