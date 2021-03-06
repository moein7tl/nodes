<?php
use app\modules\post\Module; 

/* @var $this \yii\web\View */
/* @var $user app\modules\user\models\User */
?>
<section class="container-fluid">
    <div class="row">
        <div class="col-xs-12 center-block centertext central">
            <img src="<?= $user->getProfilePicture(); ?>" class="img-circle" width="200" style="margin-top: 15px;">    
            <h1><?= $user->getName(); ?></h1>
            <p><?= $user->getTagLine(); ?></p>            
            <div class="follow">
                <?= $this->render('_follow',['user'=>$user]);?>
            </div>
        </div>
    </div>
</section>
<section class="container-fluid" id="details">
    <hr class="title">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
            <div class=" center-block centertext text-center">
                <div class="top-buffer"></div>
                <div class="row">
                    <div class="col-md-4 col-sm-4 col-xs-12 othermedia">
                        <h3><?= Module::t('post','user._user.othermedia'); ?></h3>
                        <?php foreach ($user->getActiveUrls() as $url): ?>
                            <a href="<?= $url->url; ?>" target="_blank">
                                <i class="<?= \app\components\Helper\Icon::getBrandIconClass($url->type); ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if ($user->following_count > 0):?>
                        <a href="<?= Yii::$app->urlManager->createUrl(["@{$user->username}/following"]); ?>">
                            <div class="col-md-2 col-sm-2 col-xs-6">
                                <h3><?= Module::t('post','user._user.following'); ?></h3>
                                <span class="counter"><?= $user->following_count; ?></span>
                            </div>                    
                        </a>
                    <?php else: ?>
                        <div class="col-md-2 col-sm-2 col-xs-6">
                            <h3><?= Module::t('post','user._user.following'); ?></h3>
                            <span class="counter"><?= $user->following_count; ?></span>
                        </div>                                        
                    <?php endif;?>

                    <?php if ($user->followers_count > 0):?>
                        <a href="<?= Yii::$app->urlManager->createUrl(["@{$user->username}/followers"]); ?>">
                            <div class="col-md-2 col-sm-2 col-xs-6">
                                <h3><?= Module::t('post','user._user.followers'); ?></h3>
                                <span class="counter"><?= $user->followers_count; ?></span>
                            </div>                    
                        </a>
                    <?php else: ?>
                        <div class="col-md-2 col-sm-2 col-xs-6">
                            <h3><?= Module::t('post','user._user.followers'); ?></h3>
                            <span class="counter"><?= $user->followers_count; ?></span>
                        </div>                                        
                    <?php endif;?>
                    
                    <?php if ($user->recommended_count > 0):?>
                        <a href="<?= Yii::$app->urlManager->createUrl(["@{$user->username}/recommends"]); ?>">
                            <div class="col-md-2 col-sm-2 col-xs-6">
                                <h3><?= Module::t('post','user._user.recommended'); ?></h3>
                                <span class="counter"><?= $user->recommended_count; ?></span>
                            </div>                        
                        </a>
                    <?php else: ?>
                        <div class="col-md-2 col-sm-2 col-xs-6">
                            <h3><?= Module::t('post','user._user.recommended'); ?></h3>
                            <span class="counter"><?= $user->recommended_count; ?></span>
                        </div>                        
                    <?php endif;?>                    
                    
                    <?php if ($user->posts_count > 0):?>
                        <a href="<?= Yii::$app->urlManager->createUrl(["@{$user->username}/posts"]); ?>">
                            <div class="col-md-2 col-sm-2 col-xs-6">
                                <h3><?= Module::t('post','user._user.posts'); ?></h3>
                                <span class="counter"><?= $user->posts_count; ?></span>
                            </div>                        
                        </a>
                    <?php else: ?>
                        <div class="col-md-2 col-sm-2 col-xs-6">
                            <h3><?= Module::t('post','user._user.posts'); ?></h3>
                            <span class="counter"><?= $user->posts_count; ?></span>
                        </div>    
                    <?php endif;?>                    
                </div>
            </div>
        </div>
    </div>
</section>
