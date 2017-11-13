<?php
use yii\helpers\Html;
use yii\grid\GridView;
use admin\widgets\Icon;
use admin\components\AdminTemplate;
use yii\widgets\ActiveForm;

$template = new AdminTemplate($this);

?>
<div class="row">
    <div class="col-sm-12">
        <?= $template->renderAlert('userTypeFlash');?>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-md-12">
        <?php
        $template->widgetBegin()
                ->widgetHeader([
                    'title' => 'Daftar Artikel',
                    'icon' => 'list-alt',
                    'iconType' => 'glyph',
                    'buttons' => [
                        Html::a(Icon::FA('plus') . 'Tambah Baru', '#', ['class'=>'btn btn-sm btn-success btn-new-form', 'id'=>'btn-new-form'])
                    ]
                ])
                ->widgetBody(['excludeCloseTag'=>true]);
        ?>
        <?php $form = ActiveForm::begin(['id'=>'form-bulk', 'action'=>['user/user-type-bulk']]); ?>
            <div class="col-md-2 col-xs-6">
                <div class="form-group">
                <?= Html::dropDownList('bulk_action', NULL, [
                        'delete'    => 'Delete',
                    ], ['prompt'=>'Choose action', 'id'=>'bulk_action', 'class'=>'form-control input-sm bulk_action'])?>
                </div>
            </div>
            <div class="clearfix">
                <?= GridView::widget([
                        'layout' => "{items}\n{pager}",
                        'dataProvider' => $dataProvider,
                        // 'filterModel' => $searchModel,
                        'columns' => [
                            [
                                //'class' => 'yii\grid\CheckboxColumn',
                                'headerOptions' => ['width'=>'40'],
                                'header' => Html::tag('div',
                                                        Html::tag('label',
                                                            Html::checkbox('selection_all', false, ['class'=>'select-on-check-all']).
                                                            Html::tag('span', '', ['class'=>'text'])
                                                        ),
                                                    ['class'=>'checkbox']),
                                'format' => 'raw',
                                'value' => function ($model, $key, $index, $column){
                                    return Html::tag('div',
                                                        Html::tag('label',
                                                            Html::checkbox('bulk_id[]', false, ['value'=>$model['key']]).
                                                            Html::tag('span', '', ['class'=>'text'])
                                                        ),
                                                    ['class'=>'checkbox']);
                                }
                            ],
                            'key',
                            'type',
                            [
                                'headerOptions'=>['width'=>50],
                                'format'=>'raw',
                                'value' => function($model){
                                    return Html::a(Icon::glyph('pencil'), 'javascript:', ['class'=>'btn btn-link btn-xs btn-edit-type', 'data'=>['type'=>$model['type'], 'key'=>$model['key']]]);
                                }
                            ]
                        ]
                    ]);
                ?>
            </div>
            <hr />
            <div class="col-md-2 col-xs-6">
                <div class="form-group">
                    <?= Html::dropDownList('bulk_action2', NULL, [
                            'delete'    => 'Delete',
                        ], ['prompt'=>'Choose action', 'id'=>'bulk_action2', 'class'=>'form-control input-sm bulk_action'])?>
                    </div>
            </div>

            <div class="clearfix"></div>
        <?php ActiveForm::end(); ?>
        <?php $template->widgetEnd()?>
    </div>
</div>

<?php

$formUserType = Html::tag('div',
                    Html::tag('div',
                        Html::tag('form',
                            Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) .
                            Html::tag('div',
                                Html::tag('label', 'Type Key', ['class'=>'control-label']) .
                                Html::textInput('UserType[key]', '', ['class'=>'form-control', 'id'=>'usertype-key', 'placeholder'=>'Type key'])
                            ,['class'=>'form-group']) .
                            Html::tag('div',
                                Html::tag('label', 'Type Name', ['class'=>'control-label']) .
                                Html::textInput('UserType[type]', '', ['class'=>'form-control', 'id'=>'usertype-type', 'placeholder'=>'Type name'])
                            ,['class'=>'form-group']) .
                            Html::tag('div',
                                Html::button('Cancel', ['class'=>'btn btn-default', 'data'=>['dismiss'=>"modal"]]) .
                                Html::submitButton('Submit', ['class'=>'btn btn-success', 'style'=>'margin-left:10px;'])
                            ,['class'=>'form-group text-right'])
                        ,['action'=>\yii\helpers\Url::to(['user/form-user-type']), 'method'=>'post'])
                    ,['class'=>'col-md-12'])
                ,['class'=>'row']);


$script = <<<JAVASCRIPT

    $('#btn-new-form').on('click', function(e){
        let dialog = bootbox.dialog({
            title : 'User Type',
            message : '<i class="fa fa-spin fa-cog"></i> &nbsp; Loading...'
        });
        dialog.init(function(e){
            dialog.find('.modal-body').html('{$formUserType}');
        });
    });

    $('.btn-edit-type').on('click', function(e){

        let key = $(this).data('key'),
            type = $(this).data('type')
            dialog = bootbox.dialog({
                title : 'User Type',
                message : '<i class="fa fa-spin fa-cog"></i> &nbsp; Loading...'
            });

        dialog.init(function(e){
            dialog.find('.modal-body').html('{$formUserType}');
            dialog.find('#usertype-key').val(key);
            dialog.find('#usertype-key').prop('readonly', true);
            dialog.find('#usertype-type').val(type);
        });
    });

JAVASCRIPT;

$this->registerJs($script);
