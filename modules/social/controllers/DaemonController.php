<?php
namespace app\modules\social\controllers;
use app\modules\social\models\Social;
use app\modules\social\models\Socialcontent;
use app\modules\social\Module;
use app\modules\post\models\Post;
use app\modules\post\models\Userrecommend;
use Yii;
class DaemonController extends \yii\console\Controller{
    
    public function init() {
        parent::init();
        set_time_limit(0);
    }

    public function actionIndex()
    {
        do
        {
            $timestamp      =   Social::find()->where('status=:status',['status'=>  Social::STATUS_ACTIVE])->min('last_used');
            $farthestTime   =   strtotime($timestamp);
            $diffTime       =   time()  -   $farthestTime;
            if ($timestamp == NULL || $diffTime < Module::CHECK_INTERVAL){
                sleep(Module::CHECK_INTERVAL - $diffTime + Module::ADDITIONAL_SLEEP_SECS);
            }
            $socials = Social::find()->where('status=:status AND last_used=:timestamp',[
                'status'    =>  Social::STATUS_ACTIVE,
                'timestamp' =>  $timestamp
            ])->limit(10)->all();
            foreach ($socials as $social){
                $this->dispatch($social);
            }
        } while(true);
    }
    
    
    private function dispatch(Social $social)
    {
        $posts = $this->getUnsharedPosts($social);
        if ($social->type === Social::TYPE_FACEBOOK){
            
        } else if ($social->type === Social::TYPE_TWITTER){
            
        } else if ($social->type === Social::TYPE_LINKEDIN){
            $this->linkedin($social,$posts);
        }
    }
    
    
    private function getUnsharedPosts(Social $social){
        $recommends =   [];
        $posts      = Post::find()->where('user_id=:user_id AND status=:status AND published_at>=:published_at',[
            'published_at'  =>  $linkedin->last_used,
            'status'        =>  Social::STATUS_ACTIVE,
            'used_id'       =>  $linkedin->user_id
        ]);        
        if ($linkedin->share === Social::SHARE_ALL){
            $recommends = Post::find()->join('LEFT JOIN', Userrecommend::tableName(),  Post::tableName().'.id = '.Userrecommend::tableName().'.post_id')
                ->where(Userrecommend::tableName().'.user_id=:user_id AND '.Userrecommend::tableName().'.created_at>=:created_at AND '.Post::tableName().'.status=:status',[
                    'user_id'       =>  $social->user_id,
                    'created_at'    =>  $social->last_used,
                    'status'        =>  Post::STATUS_PUBLISH
                ])->all();
        }
        return array_merge($recommends,$posts);
    }

    private function linkedin(Social $linkedin,$posts)
    {
        foreach ($posts as $post){
            $li         =   new \LinkedIn\LinkedIn([
                'api_key'       =>  Yii::$app->socialClientCollection->getClient('linkedin')->clientId,
                'api_secret'    =>  Yii::$app->socialClientCollection->getClient('linkedin')->clientSecret,
                'callback_url'  =>  Yii::$app->params['linkedinCallbackUrl']
            ]);
            $token  =   unserialize($linkedin->token);
            $li->setAccessToken($token->getAccessToken()->getToken());
            $content    =    [
                'title'         =>  $post->title,
                'submitted-url' =>  Yii::$app->urlManager->createAbsoluteUrl([
                            "{$post->user->getUsername()}/{$post->url}",
                            "utm_source"    =>  'linkedin',"UTM_CAMPAIGN"  =>  'auto-'.md5($post->user->get)]),
            ];
            if ($post->cover === Post::COVER_BYCOVER){
                $content['submitted-image-url'] =   Post::getCoverUrl($post->id);
            }
            $params =   [
                'content'       =>  $content,
                'visibility'    =>  [
                    'code'  =>  'anyone'
                ]
            ];
            
            $response = $li->post('people/~/shares', $params);
            $this->addSocialContent($social, $post, $params,$response);
        }
        $linkedin->last_used    =   new \yii\db\Expression('NOW()');
        $linkedin->update();
    }
    private function addSocialContent($social,$post,$request,$response = NULL)
    {
        $verbose            =   [
            'request'   =>  $request,
            'response'  =>  $response
        ];
        $model              =   new Socialcontent;
        $model->post_id     =   $post->id;
        $model->social_id   =   $social->id;
        $model->verbose     =   \yii\helpers\Json::encode($verbose);
        $model->save();
    }
}