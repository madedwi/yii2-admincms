<?php

use yii\helpers\Html;
use yii\grid\GridView;
use admin\widgets\Icon;
use admin\components\AdminTemplate;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CommentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Comments';
$this->params['breadcrumbs'][] = $this->title;
$template = new AdminTemplate($this);
?>
<div class="row">
    <div class="col-xs-12 col-md-12">
        <?php
        $template->widgetBegin()
                ->widgetHeader([
                    'title' => 'Comments',
                    'icon' => 'list-alt',
                    'iconType' => 'glyph',
                ])
                ->widgetBody(['excludeCloseTag'=>true]);
        ?>
        <?php $form = ActiveForm::begin(['id'=>'form-bulk', 'action'=>['bulk']]); ?>
            <div class="row">
                <div class="col-md-2 col-xs-6">
                    <div class="form-group">
                    <?= Html::dropDownList('bulk_action', NULL, [
                            'delete'    => 'Delete',
                            'draft'     => 'Status Unpublished',
                            'publish'   => 'Status Pulished'
                        ], ['prompt'=>'Choose action', 'id'=>'bulk_action', 'class'=>'form-control input-sm bulk_action'])?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?= GridView::widget([
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
                            [
                                'attribute' => 'content',
                                'format' => 'raw',
                                'value' => function($model){
                                    $html  = "Comment Post By : " . $model->comment_name . ' (' . Html::a($model->comment_email, 'mailto:'.$model->comment_email) .') ';
                                    $html .= !empty($model->comment_website) ? " / {$model->comment_website}" : "";
                                    $html .= "<br />";
                                    $html .= "Comment Post Date : " . $model->postdate;
                                    $html .= "<br />";
                                    $html .= "<br />";
                                    $html .= Html::encode($model->content);
                                    return $html;
                                }
                            ],
                            [
                                'label' => 'Article / Page',
                                'headerOptions' => ['width'=>200],
                                'format' => 'raw',
                                'value' => function($model){

                                    if($model->parent_type=='post'){
                                        $html  = Html::a($model->parent_title, ['post/update', 'id'=>$model->parent]);
                                        $html .= "<br />";
                                        $html .= '(Article)';
                                    }else if($model->parent_type=='page'){
                                        $html  = Html::a($model->parent_title, ['page/update', 'id'=>$model->parent]);
                                        $html .= "<br />";
                                        $html .= '(Page)';
                                    }else if($model->parent_type == 'comment'){
                                        $parent = $model->parentComment;
                                        $post = $parent->post;
                                        if($post->type=='post'){
                                            $html  = Html::a($post->title, ['post/update', 'id'=>$post->id]);
                                            $html .= "<br />";
                                            $html .= '(Article)';
                                        }else if($post->type=='page'){
                                            $html  = Html::a($post->title, ['page/update', 'id'=>$post->id]);
                                            $html .= "<br />";
                                            $html .= '(Page)';
                                        }
                                    }

                                    return $html;

                                },
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'headerOptions' => ['width'=>'150', 'class'=> 'text-center'],
                                'value' => function($model){
                                    if($model->status == 'publish'){
                                        return Html::tag('label', 'Published', ['class'=>'label label-success', 'style'=>'margin:0 auto; display:block;']);
                                    }else{
                                        return Html::tag('label', 'Draft', ['class'=>'label label-default', 'style'=>'margin:0 auto; display:block;']);
                                    }
                                }
                            ]

                        ],
                    ]); ?>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-md-2 col-xs-6">
                    <div class="form-group">
                    <?= Html::dropDownList('bulk_action2', NULL, [
                            'delete'    => 'Deleted',
                            'draft'     => 'Status Unpublished',
                            'publish'   => 'Status Pulished'
                        ], ['prompt'=>'Choose action', 'id'=>'bulk_action2', 'class'=>'form-control input-sm bulk_action'])?>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <?php ActiveForm::end(); ?>
        <?php $template->widgetEnd()?>
    </div>
</div>
