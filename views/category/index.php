<?php

use yii\helpers\Html;
use yii\grid\GridView;
use admin\widgets\Icon;
use admin\components\AdminTemplate;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CateoriesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;
$template = new AdminTemplate($this);
?>
<div class="row">
    <div class="col-xs-12 col-md-12">
        <?php
        $template->widgetBegin()
                ->widgetHeader([
                    'title' => 'Categories',
                    'icon' => 'list-alt',
                    'iconType' => 'glyph',
                    'buttons' => [
                        Html::a(Icon::FA('plus') . 'Tambah Baru', ['create'], ['class'=>'btn btn-sm btn-success btn-new-form'])
                    ]
                ])
                ->widgetBody(['excludeCloseTag'=>true]);
        ?>
        <div class="row">
            <div class="col-sm-12">
                <?= $template->renderAlert('post-categories');?>
            </div>
        </div>
        <?php $form = ActiveForm::begin(['id'=>'form-bulk', 'action'=>['category/bulk']]); ?>
            <div class="row">
                <div class="col-md-2 col-xs-6">
                    <div class="form-group">
                    <?= Html::dropDownList('bulk_action', NULL, [
                            'delete'    => 'Hapus'
                        ], ['prompt'=>'Pilih Aksi', 'id'=>'bulk_action', 'class'=>'form-control input-sm bulk_action'])?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?= GridView::widget([
                        'layout' => "{items}\n{pager}",
                        'dataProvider' => $dataProvider,
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

                            'terms',
                            'terms_slug',
                            // 'category_description:ntext',
                            'parentCategory.terms',

                            // ['class' => 'yii\grid\ActionColumn'],
                        ],
                    ]); ?>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-md-2 col-xs-6">
                    <div class="form-group">
                    <?= Html::dropDownList('bulk_action2', NULL, [
                            'delete'    => 'Hapus'
                        ], ['prompt'=>'Pilih Aksi', 'id'=>'bulk_action2', 'class'=>'form-control input-sm bulk_action'])?>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <?php ActiveForm::end(); ?>
        <?php $template->widgetEnd()?>
    </div>
</div>
