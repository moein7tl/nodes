<?php
namespace app\modules\user\controllers;

use Yii;
use app\modules\user\Module;
use app\modules\user\models\User;
use app\modules\post\models\Comment;

class EmailContentActicityBase extends \yii\console\Controller{
    public function init() {
        parent::init();
        set_time_limit(0);
    }    
    
    
    protected function getUsers($timestamp,$content_activity = User::ACTIVITY_SETTING_FULL,$limit = 10)
    {
        $users  =   User::find()
                    ->where('status=:status AND content_activity=:content_activity AND last_content_activity_mail=:last_content_activity_mail',[
                        ':status'                           =>      User::STATUS_ACTIVE,
                        ':content_activity'                 =>      $content_activity,
                        ':last_content_activity_mail'       =>      $timestamp
                    ])->limit($limit)->all();
        
        return $users;
    }
    
    protected function getComments($userId,$limit = 75)
    {
        return Comment::getLastUnsentComments($userId, $limit);
    }    
    
    protected function updateUserContentActivityTime(User $user)
    {
        $user->last_content_activity_mail =   new \yii\db\Expression('NOW()');
        $user->save(false);
    }

    protected function sendFullContentActivityEmail(User $user,  Comment $comment)
    {
        return \Yii::$app->mailer
                    ->compose('@mail/content_activity/full', ['user' => $user,'comment'=>$comment])
                    ->setSubject(Module::t('mail','comment.full.title'))
                    ->setFrom([Yii::$app->params['noreply-email']  =>  Module::t('mail','sender.name')])                    
                    ->setTags(['full','comment',  Yii::$app->name])
                    ->setTo($user->email)
                    ->send();        
    }
    
    protected function sendDigestContentActivityEmail(User $user,array $comments)
    {
        return \Yii::$app->mailer
                    ->compose('@mail/content_activity/digest', ['user' => $user,'comments'=>$comments])
                    ->setSubject(Module::t('mail','digest.title'))
                    ->setFrom([Yii::$app->params['noreply-email']  =>  Module::t('mail','sender.name')])                    
                    ->setTags(['digest','comment',  Yii::$app->name])
                    ->setTo($user->email)
                    ->send();        
    }

    public function setCommentsAsSent(array $comments){
        $commentsId =   [];
        
        foreach ($comments as $comment)
        {
            $commentsId[]   =   $comment->id;
        }
        
        Comment::updateAll(['notification_mail_status'=>  Comment::NOTIFICATION_MAIL_STATUS_SENT],
                           ['AND','notification_mail_status =:notification_mail_status',['in','id',$commentsId]],
                           [':notification_mail_status'     => Comment::NOTIFICATION_MAIL_STATUS_NOT_SEND,]);
    }
    
    protected function isDigestShouldBeSend(User $user,array $comments)
    {
        $diff       =   time()  -   strtotime($user->last_content_activity_mail);
        if ($diff >= Module::WEEK_SECONDS){
            return true;
        } else if ($diff >= 4 * Module::DAY_SECONDS && count($comments) >= 2) {
            return true;
        } else if ($diff >= 3 * Module::DAY_SECONDS && count($comments) >= 3) {
            return true;
        } else if ($diff >= 2 * Module::DAY_SECONDS && count($comments) >= 4) {
            return true;
        } else if ($diff >= Module::DAY_SECONDS && count($comments) >= 5) {
            return true;
        } else if (count($comments) >= 6) {
            return true;
        }
        return FALSE;
    }
}