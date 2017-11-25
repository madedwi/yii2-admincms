<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use admin\components\AdminTemplate;
use admin\widgets\Icon;


$this->title = 'Frontend Menu';

$template = new AdminTemplate($this);
$adminAsset = admin\assets\AdminAsset::register($this);
?>
<div class="row">
    <div class="col-sm-12">
        <?= $template->renderAlert('menu_config_flash');?>
    </div>
</div>
