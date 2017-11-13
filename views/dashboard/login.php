<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Login';
?>

<div class="login-container animated fadeInDown">
    <div class="loginbox bg-white">
        <div class="loginbox-title">SIGN IN</div>
        <?php
        $form = ActiveForm::begin();
        ?>
            <div class="loginbox-textbox">
                <?= $form->field($model, 'username')->textInput(['maxlength'=>true, 'placeholder'=>'Username'])->label(false) ?>
            </div>
            <div class="loginbox-textbox">
                <?= $form->field($model, 'password')->passwordInput(['maxlength'=>true, 'placeholder'=>'Password'])->label(false) ?>
            </div>
            <div class="loginbox-textbox">
                <div class="checkbox">
                    <label>
                        <?= Html::activeInput('checkbox', $model, 'rememberMe')?>
                        <span class="text">Remember Me</span>
                    </label>
                </div>
            </div>
            <div class="loginbox-submit">
                <input type="submit" class="btn btn-primary btn-block" value="Login">
            </div>

            <div class="loginbox-forgot">
                <!-- a href="">Forgot Password?</a -->
                <center><?= Html::a('Forgot Password', ['/administrator/forgot-password']) ?></center>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="logobox">
    </div>
</div>
