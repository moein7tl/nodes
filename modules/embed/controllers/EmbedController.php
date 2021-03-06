<?php

namespace app\modules\embed\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\validators\UrlValidator;
use yii\validators\RangeValidator;
use Embedly\Embedly;


use app\modules\embed\models\Embed;



class EmbedController extends \yii\web\Controller
{
    
    public function behaviors()
    {
        return [
            'access'    =>  [
                'class' =>  AccessControl::className(),
                'only'  =>  ['embed'],
                'rules' =>  [
                    [
                        'actions'       =>  ['embed'],
                        'roles'         =>  ['@'],                        
                        'matchCallback' =>  function($rule,$action){
                            Yii::$app->response->format =   Response::FORMAT_JSON;
                            $referrer                   =   Yii::$app->request->getReferrer();
                            $serverName                 =   Yii::$app->request->getServerName();
                            $parse                      =   parse_url($referrer);
                            if (array_key_exists('host', $parse) && $parse['host'] == $serverName){
                                return TRUE;
                            }
                            return FALSE;
                        },
                        'allow'         =>  TRUE
                    ]
                ],                                
            ]
        ];
    }
    
    public function actionEmbed($url,$type = 'oembed')
    {
        Yii::$app->response->format =   Response::FORMAT_JSON;
        $url                        =   $this->reassembleUrl($url);
        $this->validateUrl($url);
        
        if ($type === 'oembed'){
            $type = Embed::TYPE_OEMBED;
        } else {
            $type = Embed::TYPE_EXTRACT;
        }
        $model  = $this->loadModel($url, $type);
        if ($model === NULL || strtotime($model->updated_at) < (time() - Yii::$app->controller->module->timeLimit)){
            $response   = $this->getEmbedResponse($url,  $type);
            if ($model != NULL){
                $model->response    = serialize($response);
                $model->frequency++;
                $model->save();
            } else {
                $model          =   new Embed;
                $model->type    =   $type;
                $model->url     =   $url;
                $model->response=   serialize($response);
                $model->save();
            }
            return $response;
        } else {
            $model->updateCounters(['frequency'=>1]);
            return unserialize($model->response);
        }                
    }

    private function isCustomEmbedUrl($url)
    {
        $parse          =   parse_url($url);
        $rangeValidator =   new RangeValidator(['range'=>Yii::$app->controller->module->customEmbedRange]);
        
        if (array_key_exists('host', $parse) AND $rangeValidator->validate(strtolower($parse['host']))){
            return TRUE;
        }
        return FALSE;
    }


    private function simulateCustomEmbedly($url,$type)
    {
        $response   =   NULL;
        $parse      =   parse_url($url);
        $host       =   $parse['host'];
        if ($type === Embed::TYPE_OEMBED){
            if (in_array($host, Yii::$app->controller->module->aparatEmbedRange)){
                $response = $this->simulateAparat($url);
            }
        } else {
            $response = $this->embedlyExtract($url);
        }
        
        return $response;
    }

    private function simulateAparat($url)
    {
        $result = new \stdClass();
        $parse                  =   parse_url($url);
        $params                 =   explode("/", $parse['path']);
        $videoHash              =   $params[2];
        $result->url            =   $url;
        $result->type           =   'video';
        $result->width          =   "responsive";
        $result->height         =   "responsive";
        $result->author_url     =   '';
        $result->author_name    =   '';
        $result->html           =   "<style>.h_iframe-aparat_embed_frame{position:relative;} .h_iframe-aparat_embed_frame .ratio {display:block;width:100%;height:auto;} .h_iframe-aparat_embed_frame iframe {position:absolute;top:0;left:0;width:100%; height:100%;}</style><div class=\"h_iframe-aparat_embed_frame\"> <span style=\"display: block;padding-top: 57%\"></span><iframe src=\"https://www.aparat.com/video/video/embed/{$videoHash}/b9fPs/vt/frame\" allowFullScreen=\"true\" webkitallowfullscreen=\"true\" mozallowfullscreen=\"true\" ></iframe></div>";
        return $result;
    }

    
    private function getEmbedResponse($url,$type = Embed::TYPE_OEMBED)
    {
        if ($this->isCustomEmbedUrl($url)){
             return $this->simulateCustomEmbedly($url, $type);
        } else if ($type === Embed::TYPE_OEMBED){
            return $this->embedlyOembed($url);
        } else {
            return $this->embedlyExtract($url);
        }
    }

    private function getEmbedlyApiObject()
    {
        $keys       =   Yii::$app->params['embedlyKeys'];
        $key        =   $keys[array_rand($keys)];
        $api        =   new Embedly([
                            'key'       =>  $key,
                            'user_agent'=>  Yii::$app->request->userAgent
                        ]);
        return $api;
    }


    private function embedlyOembed($url)
    {
        $api    =   $this->getEmbedlyApiObject();
        return $api->oembed($url);
    }

    private function embedlyExtract($url)
    {
        $api    =   $this->getEmbedlyApiObject();
        return $api->extract($url);
    }
    
    
    private function validateUrl($url)
    {
        $validator  =   new UrlValidator();
        if (!$validator->validate($url)){
            throw new \yii\web\BadRequestHttpException;
        }
    }
    
    private function reassembleUrl($url)
    {
        $result = strrev($url);
        $result = trim($result);
        $parse  = parse_url($result);
        if (!array_key_exists('scheme', $parse)){
            $result = 'http://'.$result;
        }
        return $result;
    }

    /**
     * 
     * @param string $url
     * @param string $type
     * @return Embed|NULL
     */
    private function loadModel($url,$type = Embed::TYPE_OEMBED)
    {
        $hash   =   md5($url);
        $embeds =   Embed::find()->where('hash=:hash AND type=:type',[':hash'=>$hash,':type'=> $type]);
        
        foreach ($embeds->each() as $embed){
            if ($embed->url === $url){
                return $embed;
            }
        }
        return NULL;
    }
}
