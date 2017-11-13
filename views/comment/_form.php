<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Comment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'parent')->textInput() ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'draft' => 'Draft', 'publish' => 'Publish', 'trash' => 'Trash', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'layout')->dropDownList([ 'singlepage' => 'Singlepage', 'bloglist' => 'Bloglist', 'contact' => 'Contact', 'other' => 'Other', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'postdate')->textInput() ?>

    <?= $form->field($model, 'publishdate')->textInput() ?>

    <?= $form->field($model, 'postby')->textInput() ?>

    <?= $form->field($model, 'modified')->textInput() ?>

    <?= $form->field($model, 'postsort')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
