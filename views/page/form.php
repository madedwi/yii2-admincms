<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use admin\widgets\Icon;
use admin\components\AdminTemplate;
/* @var $this yii\web\View */
/* @var $model common\models\Page */
/* @var $form yii\widgets\ActiveForm */

$this->title = $model->isNewRecord ? 'Halaman Baru' : $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Halaman', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$parentList = ArrayHelper::map($parent, 'id', 'title');
$template = new AdminTemplate($this);
$adminAsset = \admin\assets\AdminAsset::register($this);

if($model->isNewRecord){
    $model->status = 'publish';
    $model->layout = 'singlepage';
}else{
    unset($parentList[$model->id]);
}
?>
        <div class="row">
            <div class="col-sm-12">
                <?= $template->renderAlert('post');?>
            </div>
        </div>

        <div class="row">
            <?php $form = ActiveForm::begin(); ?>

            <div class="col-md-9 col-sm-12">
                <div class="clearfix">
                    <?php
                    $template->widgetBegin()
                            ->widgetHeader([
                                'title' => 'Form Data',
                                'icon' => 'pencil',
                                'iconType' => 'FA',
                            ])
                            ->widgetBody(['excludeCloseTag'=>true]);
                    ?>
                        <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'class'=>'form-control input-title slug-source', 'data'=>['slugspan'=>'.slugtext', 'createslug' => $model->isNewRecord ]]) ?>

                        <?= $this->render('@admin/views/layouts/slug-input', [
                                'form' => $form,
                                'model' => $model,
                                'attribute' => 'slug',
                                'slugformat' => "{[slug]}"
                            ])?>

                        <?= $form->field($model, 'content')->textarea(['rows' => 6, 'id'=>'page_content']) ?>

                    <?php $template->widgetEnd()?>
                </div>
                <div class="clearfix">
                    <?php
                    $template->widgetBegin()->widgetHeader([
                        'title' => 'SEO Settings',
                        'icon'  => 'globe',
                        'iconType' => 'FA'
                    ])
                    ->widgetBody(['excludeCloseTag'=>true]);
                    ?>
                        <?= $form->field($model, 'seo_title')->textInput(['maxlength'=>true, 'placeholder'=>'Your content title.']) ?>
                        <?= $form->field($model, 'seo_keyword')->textInput(['maxlength'=>true, 'placeholder'=>'Seperate each keyword by comma.']) ?>
                        <?= $form->field($model, 'seo_description')->textArea(['rows'=>3, 'placeholder'=>'Describe your content.'])?>

                    <?php $template->widgetEnd(); ?>
                </div>
                <?php
                if(isset(Yii::$app->params['page_metas'])){
                    $custom_metas = Yii::$app->params['page_metas'];
                    // print_r($custom_metas);
                    foreach ($custom_metas as $metaGroup) {
                        $groupLabel = isset($metaGroup['meta_group_label']) ? $metaGroup['meta_group_label'] : '';
                        $inputs     = isset($metaGroup['meta_input']) ? $metaGroup['meta_input'] : [];
                        echo '<div class="clearfix">';
                        $template->widgetBegin()
                                ->widgetHeader([
                                    'title' => $groupLabel,
                                    'icon' => isset($metaGroup['icon']) ? $metaGroup['icon'] : 'cog',
                                    'iconType' => 'FA',
                                ])
                                ->widgetBody(['excludeCloseTag'=>true]);

                            echo $this->render('/layouts/custom_meta_input', [
                                'model' => $model,
                                'inputs' => $inputs,
                                'form' => $form
                            ]);

                        $template->widgetEnd();
                        echo '</div>';
                    }
                }

                ?>

            </div>
            <div class="col-md-3 col-sm-12">
                <div class="clearfix">
                    <?php
                    $template->widgetBegin()
                            ->widgetHeader([
                                'title' => 'Simpan Halaman',
                                'icon' => 'cog',
                                'iconType' => 'FA',
                            ])
                            ->widgetBody(['excludeCloseTag'=>true]);
                    ?>

                        <?= $form->field($model, 'parent')->dropDownList($parentList, ['prompt' => 'Page Parent']) ?>

                        <?= $form->field($model, 'status')->dropDownList($model->statusList, ['prompt' => 'Status Halaman']) ?>

                        <?= $form->field($model, 'layout')->dropDownList($model->layoutList, ['prompt' => 'Jenis Layout']) ?>

                        <?= Html::tag('div',
                                        Html::tag('div',
                                            Html::submitButton(Icon::FA('save') . '&nbsp;Simpan', ['class'=>'btn btn-success col-xs-5', 'style'=>'margin-right:10px;margin-left:15px;']) .
                                            Html::resetButton(Icon::FA('refresh') . '&nbsp;Batal', ['class'=>'btn btn-warning col-xs-5']),
                                        ['class'=>'row']),
                                        ['class'=>'form-group'])?>


                    <?php $template->widgetEnd()?>
                </div>
                <div class="clearfix">
                    <?php
                    $template->widgetBegin()
                            ->widgetHeader([
                                'title' => 'Gambar',
                                'icon' => 'image',
                                'iconType' => 'FA',
                            ])
                            ->widgetBody(['excludeCloseTag'=>true]);
                    ?>
                    <div class="thumbnail header-img">
                        <!-- img src="" title="Header image" alt="header-image" class="img-responsive" /-->
                        <?php
                        $header_img = explode('/', $model->header_img);
                        $header_img = str_replace(end($header_img), "small/" . end($header_img), $model->header_img);
                        ?>
                        <?= Html::img(empty($model->header_img) ? "{$adminAsset->baseUrl}/img/placeholder.jpg" : $header_img, ['title'=>'Header Image', 'alt'=>'header-img', 'class'=>'img-responsive'])?>
                        <?= $form->field($model, 'header_img', ['options'=>['class'=>'hidden']])->hiddenInput(['id'=>'header_img'])->label(false) ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-block btn-default" id="btn-manager">Pilih Gambar</button>
                    <?php $template->widgetEnd()?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>


<?php
\admin\assets\EditorAsset::register(Yii::$app->view);
\admin\assets\MediaManagerAsset::register(Yii::$app->view);
$script = <<<JAVASCRIPT




    var med = MediaManager();
    med.init({
        controllerUrl : admin.baseUrl +"/media/index",
        basePath     : 'uploads/images'
    });


    var editor = $('#page_content').editor({
        height:400,
        mediaManager : med
    });


    $('#btn-manager').click(function(e){
        med.open(function(image){
            $('#header_img').val(image.fileurl);
            $('.header-img>img').attr('src', image.fileurl);
        });
    })

JAVASCRIPT;

$this->registerJs($script);
?>
