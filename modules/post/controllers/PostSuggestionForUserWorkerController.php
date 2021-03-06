<?php
namespace app\modules\post\controllers;

use Yii;
use app\components\Broker;
use yii\helpers\Json;
use yii\db\Query;
use app\modules\post\models\Post;
use app\modules\user\models\Following;
use app\modules\post\models\Userrecommend;
use app\modules\post\models\Userread;
use app\modules\post\models\Guestread;
use app\modules\post\models\UserToRead;

class PostSuggestionForUserWorkerController extends \yii\console\Controller
{
    private $posts;
    
    public function init() {
        parent::init();
        set_time_limit(0);
        register_shutdown_function(function (){
            Broker::close();
        });
    }
    
    public function actionIndex(){
        Broker::consumeMessage('PostSuggestionForUser', function($message){
            //@todo this algorithm works fine,but need rewrite
            $params     =   Json::decode($message->body);
            $this->updateRankedReadedPostPriority($params['userId']);
            $this->generateUnreadedPostsRank($params['userId']);
            $this->updatePostRank($params['userId'],1);            
            $this->generateUnrankedPostsRank($params['userId']);
            $this->updatePostRank($params['userId'],2);
            Broker::sendAck($message);
        });
    }        
    
    private function updateRankedReadedPostPriority($userId)
    {
        $query  =   (new Query)
                    ->select('DISTINCT '.Userread::tableName().'.post_id')
                    ->from(Userread::tableName())
                    ->leftJoin(UserToRead::tableName(),Userread::tableName().'.post_id = '.UserToRead::tableName().'.post_id AND '.Userread::tableName().'.user_id = '.UserToRead::tableName().'.user_id')
                    ->where(UserToRead::tableName().'.priority = :priority AND '.UserToRead::tableName().'.user_id = :user_id',[':user_id'  =>  $userId,':priority' =>  1]);                
        
        $result =   $query->each();
        $posts  =   [];
        foreach ($result  as $row){
            $posts[]    =   $row['post_id'];
        }
        
        UserToRead::updateAll(['priority'=>2],['AND','user_id = :user_id AND priority =:priority',['in','post_id',$posts]],[
            ':priority'     =>  1,
            ':user_id'      =>  $userId
        ]);
    }

    private function generateUnreadedPostsRank($userId)
    {
        $this->posts                    =   [];
        //id, comments_count, published_at, pure_text
        $friendsPost                 =   $this->getUnreadedFriendsPost($userId);
        $friendsRecommendedPost      =   $this->getFriendsRecommendedPost($userId);
        $lastUnreadedPost            =   $this->getLastUnreadedPost($userId);
        
        foreach ($friendsPost as $post)
        {
            $score  =   $this->generateWordsCountRank($post['pure_text']);
            $score  +=  50; // My Friend Wrote It
            $score  +=  $this->generateCommentsCountRank($post['comments_count']);
            
            $this->addPostScore($post['id'], $score);
        }
        
        foreach ($friendsRecommendedPost as $post)
        {
            $score  =  10; // My Friend Recommend It
            // if not calcuate before
            if (!key_exists($post['id'], $this->posts)){
                $score  +=  $this->generateWordsCountRank($post['pure_text']);
                $score  +=  $this->generateCommentsCountRank($post['comments_count']);
            }

            $this->addPostScore($post['id'], $score);
        }
        
        $current   =   time();
        
        foreach ($lastUnreadedPost as $post)
        {
            $score  =   $this->generateTimeBasedRank($current, strtotime($post['published_at']));
            // if not calcuate before
            if (!key_exists($post['id'], $this->posts)){
                $score  +=  $this->generateWordsCountRank($post['pure_text']);
                $score  +=  $this->generateCommentsCountRank($post['comments_count']);
            }
            
            $this->addPostScore($post['id'], $score);
        }
    }
    
    private function generateUnrankedPostsRank($userId)
    {
        $this->posts                    =   [];
        $unRankedPosts                  =   $this->getUnrankedPosts($userId);
        $score                          =   255;
        
        foreach ($unRankedPosts as $post){
            if ($score-- > 0){
                $this->addPostScore($post['id'],$score);
            } else {
                $this->addPostScore($post['id'],0);
            }
        }
    }

    private function updatePostRank($userId,$priority = 1)
    {
        $rows       =   [];
        $posts      =   [];
        
        $timestamp  =   date("Y-m-d H:i:s", time());    
        
        foreach ($this->posts as $postId => $score){
            $posts[]    =   $postId;
            if ($score < 255){
                $rows[] =   [$userId,$postId,  round($score),$priority,$timestamp];    
            } else {
                $rows[] =   [$userId,$postId,  255,$priority,$timestamp];
            }
        }
        
        if (count($rows) >= 1){
            \Yii::$app->db->createCommand()
                ->batchInsert(UserToRead::tableName(), ['user_id','post_id','score','priority','created_at'], $rows)
                ->execute();

            //@todo fix it,using query
            UserToRead::deleteAll(['AND','user_id = :user_id AND created_at < :timestamp',['in','post_id',$posts]],[
                ':timestamp'    =>  $timestamp,
                ':user_id'      =>  $userId
            ]);
        }
    }

    private function getUnrankedPosts($userId)
    {
        $query      =   (new Query)
                        ->select(Post::tableName().'.id,'.Post::tableName().'.score')
                        ->from(Post::tableName())
                        ->leftJoin(UserToRead::tableName(),Post::tableName().'.id = '.UserToRead::tableName().'.post_id AND '.UserToRead::tableName().'.user_id=:user_id',[':user_id'=>$userId])
                        ->where(Post::tableName().'.status = :status',[':status'   =>  Post::STATUS_PUBLISH])
                        ->andWhere(UserToRead::tableName().'.id IS NULL')
                        ->orderBy(Post::tableName().'.score DESC,'.Post::tableName().'.published_at');
        return $query->each();
    }

    private function getUnreadedFriendsPost($userId)
    {
        $query  =   new Query;
        $query->select(Post::tableName().'.id, comments_count, published_at, pure_text')
                ->from(Post::tableName())
                ->leftJoin(Following::tableName(),  Post::tableName().'.user_id = '. Following::tableName().'.followed_user_id')
                ->where(Post::tableName().'.status = :status',[':status'=>Post::STATUS_PUBLISH])
                ->andWhere(Following::tableName().'.user_id = :user_id', [':user_id'=>$userId])
                ->andWhere(Post::tableName().'.published_at > DATE_SUB(now(), INTERVAL 100 DAY)')
                ->andWhere(Post::tableName().'.id NOT IN (SELECT DISTINCT post_id FROM '.Userread::tableName().' WHERE '.Userread::tableName().'.user_id = :user_id)',[':user_id'=>$userId])
                ->orderBy(Post::tableName().'.published_at');
        return $query->each();
    }
    
    private function getFriendsRecommendedPost($userId)
    {
        $query  =   new Query;
        $query->select(Post::tableName().'.id, comments_count, published_at, pure_text')                
                ->from(Post::tableName())
                ->leftJoin(Userrecommend::tableName(), Post::tableName().'.id = '.Userrecommend::tableName().'.post_id')
                ->where(Post::tableName().'.status = :status',[':status'=>Post::STATUS_PUBLISH])
                ->andWhere(Post::tableName().'.user_id != :user_id', [':user_id'=>$userId])
                ->andWhere(Post::tableName().'.published_at > DATE_SUB(now(), INTERVAL 100 DAY)')
                ->andWhere(Post::tableName().'.id NOT IN (SELECT DISTINCT post_id FROM '.Userread::tableName().' WHERE '.Userread::tableName().'.user_id = :user_id)',[':user_id'=>$userId])
                ->andWhere(Userrecommend::tableName().'.user_id IN (SELECT DISTINCT followed_user_id FROM '.Following::tableName().' WHERE '.Following::tableName().'.user_id = :user_id)',[':user_id'=>$userId])
                ->orderBy(Post::tableName().'.published_at');
        return $query->each();
    }
    
    private function getLastUnreadedPost($userId)
    {
        $subQuery1  =   '(SELECT COUNT(*) FROM '.Userread::tableName().' WHERE '.Post::tableName().'.id = '.Userread::tableName().'.post_id)';
        $subQuery2  =   '(SELECT COUNT(*) FROM '.Guestread::tableName().' WHERE '.Post::tableName().'.id = '.Guestread::tableName().'.post_id)';
        $subQuery3  =   '(SELECT DISTINCT post_id FROM '.Userread::tableName().' WHERE user_id = :user_id)';
        
        $query  =   (new Query)
                    ->select('id, comments_count, published_at, pure_text,'.$subQuery1.' AS user_read_count,'.$subQuery2.' AS guest_read_count')
                    ->from(Post::tableName())
                    ->where('status=:status',[':status'=>Post::STATUS_PUBLISH])
                    ->andWhere('published_at > DATE_SUB(now(), INTERVAL 100 DAY)')
                    ->andWhere('id NOT IN '.$subQuery3,[':user_id'=>$userId]);
        return $query->each();
    }    
    
    private function addPostScore($postId,$score = 0)
    {
        if (!key_exists($postId, $this->posts)){
            $this->posts[$postId]   =   0;
        }
        $this->posts[$postId]   +=  $score;
    }
    
    private function generateWordsCountRank($pureText)
    {
        /**
         * Function : [0,3200]  => -(x^2 - 3200x) / 51200
         * http://www.wolframalpha.com/input/?i=-%28x%5E2+-+3200x%29+%2F+51200
         * Function : [0,+INF]  =>  0
         */
        $count  =   count(preg_split('~[^\p{L}\p{N}\']+~u',$pureText));
        if ($count < 3200){
            return ((-1) * ($count * $count - 3200 * $count)) / 51200;
        }
        return 0;
    }
    
    private function generateCommentsCountRank($commentsCount)
    {
        /**
         * Absolutely need better determination
         * Function:    [0,10]      =>  x
         * Function:    [11,+INF]   =>  10
         */
        if ($commentsCount <= 10){
            return $commentsCount;
        }
        return 10;
    }
    
    private function generateTimeBasedRank($current,$timestamp)
    {
        if ($current > $timestamp){
            $x      =   $current    -   $timestamp;
            $result =   (-20 * $x) / (2160000) + 20;
            if ($result >= 0){
                return $result;
            }
        }
        return 0;
    }
}
