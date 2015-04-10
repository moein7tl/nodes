<?php
/* @var $this \yii\web\View */
/* @var $model app\modules\post\models\Post */
use yii\helpers\Html;
use app\modules\post\Module;
use app\modules\post\models\Post;
use app\modules\post\models\Userrecommend;
use yii\widgets\Pjax;
?>
<header class="main-header">
    <nav class="navbar navbar-fixed-top navbar-default" role="navigation">
        <div class="navbar-custom-menu pull-right">
            <?php if ($model->user_id != Yii::$app->user->getId()):?>
                <ul class="nav navbar-nav flaty-nav pull-right">
                    <li class="hidden-xs">
                        <a href="<?= Yii::$app->urlManager->createUrl(['/post/edit','id'=>  base_convert($model->id, 10, 36)]) ?>">
                            <?php echo Module::t('post','header.view.edit'); ?>
                            <i class="glyphicon glyphicon-pencil"></i>                
                        </a>
                    </li>          
                </ul>
            <?php else:?>
                <?php if (Userrecommend::getPostRecommended($model->id) == NULL):?>
                    <ul class="nav navbar-nav flaty-nav pull-right">
                        <li class="hidden-xs">
                            <a href="#" id="recommend">
                                <?php echo Module::t('post','header.view.recommend'); ?>
                                <i class="glyphicon glyphicon-star-empty"></i>
                            </a>
                        </li>          
                    </ul>   
                <?php else:?>
                    <ul class="nav navbar-nav flaty-nav pull-right">
                        <li class="hidden-xs">
                            <a href="#" id="recommend">
                                <?php echo Module::t('post','header.view.recommended'); ?>
                                <i class="glyphicon glyphicon-star"></i>
                            </a>
                        </li>
                    </ul>                           
                <?php endif;?>
            <?php endif;?>
        </div>
        <div class="navbar-custom-menu pull-left">
            <ul class="nav navbar-nav flaty-nav pull-left">
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">               
                        <i class="glyphicon hidden-xs"><?= Yii::$app->user->getIdentity()->getUsername();?></i>                
                        <i class="glyphicon hidden-lg hidden-md hidden-sm">@</i>  
                        <img src="<?= Yii::$app->user->getIdentity()->getProfilePicture();?>" class="user-image" alt="<?= Yii::$app->user->getIdentity()->getName();?>"/>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="user-footer">      
                            <?= Html::a(Yii::t('app','header.user.newpost').'<i class="glyphicon glyphicon-pencil"></i>',
                                        Yii::$app->urlManager->createUrl(['/post/write','type'=>'new']));?>
                            <hr class="mini central">
                            <?= Html::a(Yii::t('app','header.user.posts').'<i class="glyphicon glyphicon-list"></i>',
                                        Yii::$app->urlManager->createUrl(['/post/admin']));?>
                            <?= Html::a(Yii::t('app','header.user.comments').'<i class="glyphicon glyphicon-comment"></i>',
                                        Yii::$app->urlManager->createUrl(['/post/comments']));?>                            
                            <!--<?= Html::a(Yii::t('app','header.user.stats').'<i class="glyphicon glyphicon-stats"></i>',
                                        Yii::$app->urlManager->createUrl(['/post/stats']));?>                    
                            <?= Html::a(Yii::t('app','header.user.publications').'<i class="glyphicon glyphicon-leaf"></i>',
                                        Yii::$app->urlManager->createUrl(['/post/publication']));?>                                        -->
                            <?= Html::a(Yii::t('app','header.user.socials').'<i class="glyphicon glyphicon-share"></i>',
                                        Yii::$app->urlManager->createUrl(['/social/admin']));?>                                        
                            <?php if (!Yii::$app->user->getIdentity()->isNameAndTaglineSet()): ?>    
                                <?= Html::a(Yii::t('app','header.user.completeYourProfile').'<i class="glyphicon glyphicon-user"></i>', Yii::$app->urlManager->createUrl(['/user/profile'])); ?>  
                            <?php else: ?>
                                <?= Html::a(Yii::t('app','header.user.profile').'<i class="glyphicon glyphicon-user"></i>', Yii::$app->urlManager->createUrl(['/'.Yii::$app->user->getIdentity()->getUsername()])); ?>
                            <?php endif; ?>            
                            <?= Html::a(Yii::t('app','header.user.setting').'<i class="glyphicon glyphicon-wrench"></i>', Yii::$app->urlManager->createUrl(['/user/setting'])); ?>
                            <?= Html::a(Yii::t('app','header.user.logout').'<i class="glyphicon glyphicon-log-out"></i>', Yii::$app->urlManager->createUrl(['/user/logout'])); ?>                                    
                        </li>
                    </ul>            
                </li>
            </ul>
        </div>    
  </nav>
</header>    