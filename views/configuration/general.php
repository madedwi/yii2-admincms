<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use admin\components\AdminTemplate;
use admin\widgets\Icon;


$this->title = 'General Settings';

$template = new AdminTemplate($this);
$adminAsset = admin\assets\AdminAsset::register($this);
?>
<div class="row">
    <div class="col-sm-12">
        <?= $template->renderAlert('general_config_flash');?>
    </div>
</div>

<div class="row">
    <?php $form = ActiveForm::begin(); ?>
        <div class="col-md-9 col-sm-8 col-xs-12">
            <div class="clearfix">
                <?php
                $template->widgetBegin()
                        ->widgetHeader([
                            'title' => 'Header Logo',
                            'icon' => 'cog',
                            'iconType' => 'FA',
                        ])
                        ->widgetBody(['excludeCloseTag'=>true]);
                ?>
                <div class="thumbnail header-img">
                    <?php
                    $header_logo_image = explode('/', $model->header_logo_image);
                    $header_logo_image = str_replace(end($header_logo_image), "small/" . end($header_logo_image), $model->header_logo_image);
                    ?>
                    <?= Html::img(empty($model->header_logo_image) ? "{$adminAsset->baseUrl}/" : $header_logo_image, ['title'=>'Header Image', 'alt'=>'header-img', 'class'=>'img-responsive', 'id'=>'preview-hreader-logo'])?>
                    <?= Html::activeHiddenInput($model, 'header_logo_image', ['id'=>'header_logo_image']); ?>
                </div>
                <?= Html::button('Select Logo', [
                                            'class'=>'btn btn-sm btn-block btn-default btn-select-image',
                                            'data'=>[
                                                'inputtarget'=>'#header_logo_image',
                                                'previewcontainer' => '#preview-hreader-logo'
                                                ]
                                            ]) ?>
                <?php $template->widgetEnd()?>

                <?php
                $template->widgetBegin()
                        ->widgetHeader([
                            'title' => 'General Settings',
                            'icon' => 'cog',
                            'iconType' => 'FA',
                        ])
                        ->widgetBody(['excludeCloseTag'=>true]);
                ?>

                <?= $form->field($model, 'web_title')->textInput(['maxlength'=>true])->label('Title') ?>

                <?= $form->field($model, 'web_tagline')->textInput(['maxlength'=>true])->label('Tagline') ?>

                <?= $form->field($model, 'admin_email')->textInput(['maxlength'=>true])->label('Email') ?>

                <?= $form->field($model, 'web_meta_keyword')->textInput(['maxlength'=>true])->label('Default Meta Keyword') ?>

                <?= $form->field($model, 'web_meta_description')->textarea(['maxlength'=>true, 'rows'=>3])->label('Default Meta Description') ?>

                <?= $form->field($model, 'timezone')->dropDownList($model->supportedTimeZone)->label('Timezone') ?>

                <?= $form->field($model, 'date_format')->textInput(['maxlength'=>true])->label('Date Format') ?>

                <?= $form->field($model, 'time_format')->textInput(['maxlength'=>true])->label('Time Format') ?>

                <hr />
                <?php
                    echo Html::beginTag('div', ['class'=>'form-group field-generaloptions-post_url_format']);
                    echo Html::tag('label', 'Post Url Format', ['class'=>'control-label', 'for'=>'generaloptions-post_url_format']);
                    echo Html::activeTextInput($model, 'post_url_format', ['class'=>'form-control']);
                    echo Html::tag('div', '',['class'=>'help-block']);
                    echo Html::beginTag('div', ['class'=>'clearfix', 'style'=>'padding-left:15px;']);
                        echo "<i>Post url will always start by \"p/\", ex : http://www.domain.com/p/you-url-format.</i>";
                        echo "<br /><br />URL segment keyword : <br />";
                        echo "<span style=\"margin-left:10px; width:180px; display:inline-block;\">{[category]}</span> => use one of post categories.<br />";
                        echo "<span style=\"margin-left:10px; width:180px; display:inline-block;\">{[publish_year]}</span> => use publish date.<br />";
                        echo "<span style=\"margin-left:10px; width:180px; display:inline-block;\">{[publish_month_numeric]}</span> => use publish date.<br />";
                        echo "<span style=\"margin-left:10px; width:180px; display:inline-block;\">{[publish_month_name]}</span> => use publish date.<br />";
                        echo "<span style=\"margin-left:10px; width:180px; display:inline-block;\">{[slug]}</span> => auto generate from post title.<br />";
                    echo Html::endTag('div');
                    echo Html::endTag('div');
                //$form->field($model, 'post_url_format')->textInput(['maxlength'=>true])->label('Url Format') ?>
                <?php $template->widgetEnd()?>

                <?php
                $template->widgetBegin()
                        ->widgetHeader([
                            'title' => 'Social Network',
                            'icon' => 'cog',
                            'iconType' => 'FA',
                        ])
                        ->widgetBody(['excludeCloseTag'=>true]);
                ?>

                <?= $form->field($model, 'facebook')->textInput(['maxlength'=>true])->label('Facebook Url') ?>

                <?= $form->field($model, 'twitter')->textInput(['maxlength'=>true])->label('Twitter Username') ?>

                <?= $form->field($model, 'instagram')->textInput(['maxlength'=>true])->label('Instagram Username') ?>

                <?= $form->field($model, 'gplus')->textInput(['maxlength'=>true])->label('Google+ Url') ?>

                <?= $form->field($model, 'pinterest')->textInput(['maxlength'=>true])->label('Pinterest') ?>

                <?php $template->widgetEnd()?>
            </div>

            <?php
            if(isset(Yii::$app->params['client_options'])){
                $custom_metas = Yii::$app->params['client_options'];
                foreach ($custom_metas as $metaGroup) {
                    echo '<div class="clearfix">';
                    $template->widgetBegin()
                            ->widgetHeader([
                                'title' => $metaGroup['options_group_label'],
                                'icon' => isset($metaGroup['icon']) ? $metaGroup['icon'] : 'cog',
                                'iconType' => 'FA',
                            ])
                            ->widgetBody(['excludeCloseTag'=>true]);

                        echo $this->render('/layouts/custom_meta_input', [
                            'model' => $model,
                            'inputs' => $metaGroup['meta_input'],
                            'form' => $form
                        ]);

                    $template->widgetEnd();
                    echo '</div>';
                }
            }

            ?>
        </div>

        <div class="col-md-3 col-sm-4 col-xs-12">

            <?php
            $template->widgetBegin()
                    ->widgetHeader([
                        'title' => 'Web Icon',
                        'icon' => 'cog',
                        'iconType' => 'FA',
                    ])
                    ->widgetBody(['excludeCloseTag'=>true]);
            ?>
            <div class="thumbnail header-img">
                <!-- img src="" title="Header image" alt="header-image" class="img-responsive" /-->
                <?php
                $favicon_img = explode('/', $model->favicon);
                $favicon_img = str_replace(end($favicon_img), "thumb/" . end($favicon_img), $model->favicon);
                ?>
                <?= Html::img(empty($model->favicon) ? "{$adminAsset->baseUrl}/img/favicon.png" : $favicon_img, ['title'=>'Header Image', 'alt'=>'header-img', 'class'=>'img-responsive', 'id'=>'preview-favicon'])?>
                <?= $form->field($model, 'favicon', ['options'=>['class'=>'hidden']])->hiddenInput(['id'=>'favicon'])->label(false) ?>
            </div>
            <?= Html::button('Select Logo', ['class'=>'btn btn-sm btn-block btn-default btn-select-image', 'data'=>['inputtarget'=>'#favicon', 'previewcontainer'=>'#preview-favicon'], 'id'=>'btn-manager']) ?>

            <?php $template->widgetEnd()?>

            <?php
            $template->widgetBegin()
                    ->widgetHeader([
                        'title' => 'Update Configuration',
                        'icon' => 'save',
                        'iconType' => 'FA',
                    ])
                    ->widgetBody(['excludeCloseTag'=>true]);
            ?>
            <div class="clearfix">
                <?= Html::submitButton(Icon::FA('save') . '&nbsp;Simpan', ['class'=>'btn btn-block btn-success']) ?>
            </div>


            <?php $template->widgetEnd()?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
<?php

\admin\assets\MediaManagerAsset::register(Yii::$app->view);
$laoderPath = Yii::getAlias('@web/js/media-manager/');
$script = <<<JAVASCRIPT
    let med = MediaManager();
    med.init({
        controllerUrl : admin.baseUrl +"/media/index",
        basePath     : 'uploads/images'
    });

    $('.btn-select-image').click(function(e){
        let inputTargt = $(this).data('inputtarget'),
            previewContainer = $(this).data('previewcontainer');
        med.open(function(image){
            $(inputTargt).val(image.fileurl);
            $(previewContainer).attr('src', image.fileurl);
        });
    });
JAVASCRIPT;

$this->registerJs($script);
