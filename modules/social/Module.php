<?php

namespace app\modules\social;


use Yii;
class Module extends \yii\base\Module
{
    
    const ADDITIONAL_SLEEP_SECS =   5;
    const CHECK_INTERVAL        =   86400;
    public $controllerNamespace =   'app\modules\social\controllers';
    public $socialTable         =   '{{%social}}';
    public $socialContentTable  =   '{{%socialcontent}}';
    
    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }
    public function registerTranslations()
    {
        Yii::$app->i18n->translations['modules/social/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@app/modules/social/messages',
            'fileMap' => [
                'modules/social/social'     => 'social.php',
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('modules/social/' . $category, $message, $params, $language);
    }    
}
