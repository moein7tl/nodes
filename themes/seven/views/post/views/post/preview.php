<?php 
/* @var $this \yii\web\View */
/* @var $post app\modules\post\models\Post */
use app\modules\post\models\Post;
use app\components\Helper\Extract;
use app\modules\post\Module;
use yii\helpers\HtmlPurifier;

\app\assets\ShowPostAssets::register($this);

$title              =   HtmlPurifier::process(Extract::extractTitle($post->autosave_content));
$content            =   HtmlPurifier::process(Extract::extractContent($post->autosave_content),app\components\Helper\Purifier::getConfig());

if ($title  == ''){
    $this->title        =   Module::t('post','preview.head.title.unknown');
} else {
    $this->title        =   Module::t('post','preview.head.title.set',['title'=>$title]);    
}

if ($post->cover === Post::COVER_BYCOVER){
    echo $this->render('post/_by_cover',['title'=>$title,'post'=>$post,'preview'=>true]);
} else {
    echo $this->render('post/_no_cover',['title'=>$title,'post'=>$post]);
}
?>
<div class="row" id="content">
    <div class="col-md-8 col-md-offset-2">    
        <div class="row">
            <div class="col-md-12">
                <article class="postArticle">
                    <div class="postContent">
                        <div class="notesSource">
                            <div id="editor">
                                <section class="section--first section--last">
                                    <div class="section-content"> 
                                        <div class="section-inner layoutSingleColumn" style="padding-top: 35px;"> 
                                            <?= $content; ?>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>
</div>
<?php 
$js=<<<JS
$("#editor a[href^='http://']").attr("target","_blank");
$("#editor a[href^='https://']").attr("target","_blank");
JS;
$this->registerJs($js);
