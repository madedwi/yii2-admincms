<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use admin\widgets\Icon;
use admin\components\AdminTemplate;
/* @var $this yii\web\View */
/* @var $model common\models\Page */
/* @var $form yii\widgets\ActiveForm */

$this->title = $model->isNewRecord ? 'Artikel Baru' : $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Artikel', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$template = new AdminTemplate($this);
$adminAsset = \admin\assets\AdminAsset::register($this);

$moduleParams = Yii::$app->getModule('administrator')->params;

if($model->isNewRecord){
    $model->status = 'publish';
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
                        <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'class'=>'form-control input-title slug-source', 'data'=>['slugspan'=>'.slugtext', 'createslug' => $model->isNewRecord]]) ?>

                        <?php // $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
                        <?= $this->render('@admin/views/layouts/slug-input', [
                                'form' => $form,
                                'model' => $model,
                                'attribute' => 'slug',
                                'slugformat' => $slugFormat
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
                if(isset(Yii::$app->params['post_metas'])){
                    $custom_metas = Yii::$app->params['post_metas'];
                    foreach ($custom_metas as $metaGroup) {
                        echo '<div class="clearfix">';
                        $template->widgetBegin()
                                ->widgetHeader([
                                    'title' => $metaGroup['meta_group_label'],
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

                            if(empty($model->publishdate) || is_null($model->publishdate)){
                                $model->publishdate = Yii::$app->getModule('administrator')->dateTime->timeFromServerZone('now')->format('Y-m-d H:i:s');
                            }else{
                                $model->publishdate = Yii::$app->getModule('administrator')->dateTime->timeFromServerZone($model->publishdate)->format('Y-m-d H:i:s');
                            }
                    ?>

                        <?= $form->field($model, 'status')->dropDownList($model->statusList, ['prompt' => 'Status Halaman']) ?>

                        <?= $form->field($model, 'publishdate')->textInput(['class'=>'form-control bs-selectdate'])->label('Tanggal Publikasi')?>

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
                                'title' => 'Terms',
                                'icon' => 'tags',
                                'iconType' => 'FA',
                            ])
                            ->widgetBody(['excludeCloseTag'=>true]);
                    ?>

                    <div class="clearfix container-terms" id="container-tags">
                        <?= $form->field($model, 'enable_comment')->checkbox(['value'=>1, 'label'=>Html::tag('span', 'Show Comment Box', ['class'=>'text'])]); ?>
                    </div>

                    <?php
                    if(count($categories) >0 ){
                        ?>
                        <div class="clearfix container-terms">
                            <?= Html::tag('label', 'Categories', ['class'=>'control-label'])?>
                            <div id="container-categories" style="max-height:150px;">
                                <?= Html::checkboxList('Post[terms][category]', $selectedCategoryIDs, $categories,
                                                ['item'=> function ($index, $label, $name, $isChecked, $value){
                                                    $checked = $isChecked ? 'checked' : '';
                                                    $chk = '<input type="checkbox" class="chk-terms" value="'.$value.'" name="'.$name.'" '.$checked.' />';
                                                    $html = Html::tag('div',
                                                                        Html::tag('label', $chk . Html::tag('span', $label, ['class'=>'text']) )
                                                                        ,
                                                                        ['class'=>'checkbox']);

                                                    return $html;
                                                }]) ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="clearfix container-terms" id="container-tags">
                        <div class="form-group">
                            <?= Html::tag('label', 'Tags', ['class'=>'control-label']) ?>
                            <?= Html::textInput('Post[terms][tag]', $selectedTags, ['class'=>'form-control', 'id'=>'tags-input']) ?>
                            <?= Html::tag('span', 'Seperate tags by commas', ['class'=>'help-text text-italic'])?>
                        </div>

                    </div>
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
                        <?= Html::img(empty($model->header_img) ? "{$adminAsset->baseUrl}/img/placeholder.jpg" : $model->header_img, ['title'=>'Header Image', 'alt'=>'header-img', 'class'=>'img-responsive'])?>
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
$laoderPath = Yii::getAlias('@web/js/media-manager/');
$script = <<<JAVASCRIPT

    var datePickerElement   = $('.bs-selectdate'),
        datePickerValue     = datePickerElement.val();


    var med = MediaManager();
    med.init({
        controllerUrl : admin.baseUrl +"/media/index",
        basePath     : 'uploads/images'
    });


    var editor = $('#page_content').editor({
        height:400,
        mediaManager : med
    });

    datePickerElement.each(function(i, elm){
        $(elm).datetimepicker({
            date : new Date(elm.value),
            format:'DD MMM YYYY, HH:mm:ss'
        }).on('dp.change', function(e){
            let targetInput = $(this).data('targetinput');
            if(targetInput.length>0){
                $(targetInput).val(e.date.format('YYYY-MM-DD HH:mm:ss'));
            }
        });
    });

    //.date(new Date(datePickerValue));

    $('#btn-manager').click(function(e){
        med.open(function(image){
            $('#header_img').val(image.fileurl);
            $('.header-img>img').attr('src', image.fileurl);
        });
    });

    $('#container-categories').slimScroll({
        height: '150px'
    });

    // configure tags input;
    var tagsInputElement = $('#tags-input');
    $('#tags-input').tagsinput({
        typeahead: {
            source: function(q){
                return $.get(admin.baseUrl +'/page/tags', {search:q});
            }
        }
    });

    // end configure tags input;


    $(document).on('click', '.chk-terms', function(e){
        let termsid = [];
        $('.chk-terms:checked').each(function(i, elm){
            termsid.push(elm.value);
        });

        if($('#slug-category').length>0){
            $.post(admin.baseUrl +'/post/slugterms', {termsid:termsid}).done(function(data){
                $('#slug-category').replaceWith(data);
            });
        }
    });

JAVASCRIPT;

$this->registerJs($script);
?>
