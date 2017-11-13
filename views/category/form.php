<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use admin\widgets\Icon;
use admin\components\AdminTemplate;

/* @var $this yii\web\View */
/* @var $model common\models\Categories */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Form Category';

$template = new AdminTemplate($this);
?>

<div class="row">
    <div class="col-xs-12 col-md-12">
        <?php
        $template->widgetBegin()
                ->widgetHeader([
                    'title' => 'Form Category',
                    'icon' => 'pencil',
                    'iconType' => 'FA',
                ])
                ->widgetBody(['excludeCloseTag'=>true]);
        ?>

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'parent')->dropDownList($parent, ['maxlength' => true, 'prompt'=>'Select category parent']) ?>

            <?= $form->field($model, 'terms')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'terms_slug')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'terms_description')->textarea(['rows' => 6]) ?>

            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        <?php $template->widgetEnd()?>
    </div>
</div>


<?php
$script = <<<JAVASCRIPT
    $('#terms-terms').on('keyup', function(e){
        var slug = convertToSlug(this.value);
        $('#terms-terms_slug').val(slug);
    });
JAVASCRIPT;

$this->registerJs($script);
?>
