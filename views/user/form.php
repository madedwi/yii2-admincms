<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use admin\widgets\Icon;
use admin\components\AdminTemplate;

/* @var $this yii\web\View */
/* @var $model admin\models\User */
/* @var $form yii\widgets\ActiveForm */
$template = new AdminTemplate($this);
?>


<div class="row">
    <div class="col-sm-12">
        <?= $template->renderAlert('user_flash');?>
    </div>
</div>

<div class="row">
    <?php $form = ActiveForm::begin(); ?>
        <div class="col-md-9 col-sm-12">
            <?php
            $template->widgetBegin()
                    ->widgetHeader([
                        'title' => 'User Form',
                        'icon' => 'pencil',
                        'iconType' => 'FA',
                    ])
                    ->widgetBody(['excludeCloseTag'=>true]);
            ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

            <hr />

            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

            <hr />

            <?php
                if(!$model->isNewRecord){
                    echo $form->field($model, 'password_old')->passwordInput(['maxlength' => true]);
                }
            ?>

            <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => true]) ?>

            <?php // $form->field($model, 'timezone')->textInput(['maxlength' => true]) ?>

            <?php // $form->field($model, 'status')->textInput() ?>


            <?php $template->widgetEnd()?>


        </div>
        <div class="col-md-3 col-xs-12">
            <?php
            $template->widgetBegin()
                    ->widgetHeader([
                        'title' => 'User Meta',
                        'icon' => 'pencil',
                        'iconType' => 'FA',
                    ])
                    ->widgetBody(['excludeCloseTag'=>true]);
            ?>

            <?= $form->field($model, 'type')->dropDownList($userTypes, ['prompt'=>'Select user type']) ?>

            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>


            <?php $template->widgetEnd()?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
