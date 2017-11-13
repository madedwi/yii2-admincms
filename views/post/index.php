<?php

use yii\helpers\Html;
use yii\grid\GridView;
use admin\widgets\Icon;
use admin\components\AdminTemplate;
use yii\widgets\ActiveForm;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\PageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pages';
$this->params['breadcrumbs'][] = $this->title;

$template = new AdminTemplate($this);
?>
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
        <?php $form = ActiveForm::begin(['id'=>'form-bulk', 'action'=>['post/bulk']]); ?>
            <div class="col-md-2 col-xs-6">
                <div class="form-group">
                <?= Html::dropDownList('bulk_action', NULL, [
                        'delete'    => 'Hapus',
                        'draft'     => 'Status Unpublished',
                        'publish'   => 'Status Pulished'
                    ], ['prompt'=>'Pilih Aksi', 'id'=>'bulk_action', 'class'=>'form-control input-sm bulk_action'])?>
                </div>
            </div>
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

                    //'id',
                    // 'title',
                    [
                        'attribute' => 'title',
                        'format' => 'raw',
                        'value' => function($model, $key, $index){
                            $html  = Html::a(Html::tag('h5', $model->title), ['update', 'id'=>$model->id]);
                            $html .= Html::tag('p', StringHelper::truncate(strip_tags($model->content), 200, '...'));

                            return $html;
                        }
                    ],

                    // 'content:ntext',
                    // 'type',
                    // 'status',
                    [
                        'attribute' => 'author.email',
                        'headerOptions' => ['width'=>'150']
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
                    ],
                    // 'layout',
                    //'postdate',
                    [
                        'attribute' => 'postdate',
                        'headerOptions' => ['width'=>'150', 'class'=> 'text-center']
                    ],
                    //'user.email',
                    // 'modifed',
                ],
            ]); ?>
            <hr />
            <div class="col-md-2 col-xs-6">
                <div class="form-group">
                <?= Html::dropDownList('bulk_action2', NULL, [
                        'delete'    => 'Hapus',
                        'draft'     => 'Status Unpublished',
                        'publish'   => 'Status Pulished'
                    ], ['prompt'=>'Pilih Aksi', 'id'=>'bulk_action2', 'class'=>'form-control input-sm bulk_action'])?>
                </div>
            </div>
            <div class="clearfix"></div>
        <?php ActiveForm::end(); ?>
        <?php $template->widgetEnd()?>
    </div>
</div>
