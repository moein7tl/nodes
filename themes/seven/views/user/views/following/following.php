<?php
use app\modules\user\Module;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */
$this->title    =   Module::t('following','following.head.title',['name'=>$user->getName()]);
$this->render('meta/_following',['user'=>$user]);
?>
<div class="top-buffer"></div>
<div class="row">   
    <div class="col-md-10 col-md-offset-1 col-xs-12">
        <div class="box box-primary">
            <div class="box-header text-center">
                <?= Module::t('following', 'following.header'); ?>
                <a href="<?= Yii::$app->urlManager->createUrl("{$user->getUsername()}")?>">
                    <?= $user->getName();?>
                </a>
            </div>
            <div class="box-body">
                <hr class="mini central">
                <?php
                    foreach($models as $model){
                        $user = $model->getFollowedUser()->one();
                        echo $this->render('_user',['user'=>$user]);
                    }
                ?>
            </div>
        </div>
    </div>    
</div>
<div class="row">
    <div class="col-md-12 centertext">
        <?= LinkPager::widget(['pagination' => $pages,'nextPageLabel'=>'&laquo;','prevPageLabel'=>'&raquo;']);?>
    </div>
</div>