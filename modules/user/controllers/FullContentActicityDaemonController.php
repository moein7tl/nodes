<?php

namespace app\modules\user\controllers;

use app\modules\user\Module;
use app\modules\user\models\User;

class FullContentActicityDaemonController extends EmailContentActicityBase
{
    public function actionIndex()
    {
        do{
            $timestamp      =   User::find()->where('status=:status AND content_activity=:content_activity',
                                            [':status'=>User::STATUS_ACTIVE,':content_activity'=>User::ACTIVITY_SETTING_FULL])
                                            ->min('last_content_activity_mail');            
            
            if ($timestamp === NULL){
                sleep(Module::MINUTE_SECONDS);
                continue;
            }

            $diff   =   time() - strtotime($timestamp);
            
            if ($diff < Module::MINUTE_SECONDS){
                sleep(Module::MINUTE_SECONDS + Module::ADDITIONAL_SLEEP_SECS - $diff);
                continue;;
            }
            
            $users  = $this->getUsers($timestamp, User::ACTIVITY_SETTING_FULL);

            foreach ($users as $user)
            {
                $comments   = $this->getComments($user->getId());
                if (count($comments) > 1){
                    $this->sendDigestContentActivityEmail($user, $comments);
                    $this->setCommentsAsSent($comments);
                } else if (count($comments) === 1){
                    $this->sendFullContentActivityEmail($user, $comments[0]);
                    $this->setCommentsAsSent($comments);
                }
                $this->updateUserContentActivityTime($user);
            }
        } while(true);
    }
}