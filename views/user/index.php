<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use admin\widgets\Icon;
use admin\components\AdminTemplate;
use yii\widgets\ActiveForm;
use admin\models\User;

/* @var $this yii\web\View */
/* @var $searchModel admin\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;

$template = new AdminTemplate($this);
?>

<div class="row">
    <div class="col-sm-12 alert-container" id="user-flash-alert">
        <?= $template->renderAlert('usersFlash');?>
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
                        Html::a(Icon::FA('plus') . 'Tambah Baru', ['create'], ['class'=>'btn btn-sm btn-success btn-new-form'])
                    ]
                ])
                ->widgetBody(['excludeCloseTag'=>true]);
        ?>
        <?php $form = ActiveForm::begin(['id'=>'form-bulk', 'action'=>['user/user-bulk']]); ?>
            <div class="col-md-2 col-xs-6">
                <div class="form-group">
                <?= Html::dropDownList('bulk_action', NULL, [
                    'delete'    => 'Delete',
                    'notactive'     => 'Not Active',
                    'active'   => 'Active'
                ], ['prompt'=>'Choose Options', 'id'=>'bulk_action', 'class'=>'form-control input-sm bulk_action'])?>
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
                                                        Html::checkbox('bulk_id[]', false, ['value'=>$key]).
                                                        Html::tag('span', '', ['class'=>'text'])
                                                    ),
                                                ['class'=>'checkbox']);
                            }
                        ],

                        // 'id',
                        'username',
                        // 'auth_key',
                        // 'password_hash',
                        // 'password_reset_token',
                        // 'timezone',
                        'email:email',
                        [
                            'attribute' => 'type',
                            'format' => 'text',
                            'value' => function($model) use($userTypes){
                                return isset($userTypes[$model->type]) ? $userTypes[$model->type] : '(not set)';
                            }
                        ],
                        [
                            'headerOptions' => ['class'=>'text-center', 'width'=>90],
                            'attribute' =>'status',
                            'format' => 'raw',
                            'value' => function($model){
                                if($model->status == User::STATUS_ACTIVE){
                                    return Html::a('Active', 'javascript:', ['class'=>'btn btn-xs btn-success btn-block btn-user-status', 'data'=>['currentstatus'=>User::STATUS_ACTIVE, 'tochange'=>User::STATUS_SUSPEND, 'ref'=>$model->id], 'title'=>'Click to update status to "Not Active".']);
                                }else if($model->status == User::STATUS_SUSPEND){
                                    return Html::a('Not Active', 'javascript:', ['class'=>'btn btn-xs btn-default btn-block btn-user-status', 'data'=>['currentstatus'=>User::STATUS_SUSPEND, 'tochange'=>User::STATUS_ACTIVE, 'ref'=>$model->id], 'title'=>'Click to update status to "Active".']);
                                }
                            }
                        ],
                        // 'created_at',
                        // 'updated_at',

                        [
                            'headerOptions' => ['class'=>'text-center', 'width'=>40],
                            'format'=>'raw',
                            'value' => function($model){
                                return Html::a(Icon::glyph('pencil'), ['update', 'id'=>$model->id], ['class'=>'btn btn-link btn-xs']);
                            }
                        ]
                    ],
                ]); ?>
            </div>
            <hr />
            <div class="col-md-2 col-xs-6">
                <div class="form-group">
                <?= Html::dropDownList('bulk_action2', NULL, [
                        'delete'    => 'Delete',
                        'notactive'     => 'Not Active',
                        'active'   => 'Active'
                    ], ['prompt'=>'Choose Options', 'id'=>'bulk_action2', 'class'=>'form-control input-sm bulk_action'])?>
                </div>
            </div>
            <div class="clearfix"></div>
        <?php ActiveForm::end(); ?>
        <?php $template->widgetEnd()?>
    </div>
</div>


<?php
$statusUrl = Url::to(['user/ajax']);
$script = <<<JAVASCRIPT
    let http = Http("{$statusUrl}");
    $('.btn-user-status').on('click', function(e){
        e.preventDefault();
        let button          = $(this),
            currentStatus   = $(this).data('currentstatus'),
            targetStatus    = $(this).data('tochange'),
            refference      = $(this).data('ref'),
            message         = targetStatus == 'active' ? "Update status to Active?" : "Update status to Not Active?";


        bootbox.confirm(message, function(c){
            if(c){
                http.post({
                    mode    : 'update-status',
                    target  : targetStatus,
                    referrence : refference
                }).resultJson().done(function(data, message){
                    renderAlert('success', message, '#user-flash-alert');
                    if(targetStatus == 'active'){
                        button.removeClass('btn-default').addClass('btn-success');
                        button.html('Active');
                    }else{
                        button.removeClass('btn-success').addClass('btn-default');
                        button.html('Not Active');
                    }

                    button.data('currentstatus', targetStatus);
                    button.data('tochange', currentStatus);

                }).fail(function(message){
                    bootbox.alert(message);
                });
            }
        })

    });
JAVASCRIPT;

$this->registerJs($script);
