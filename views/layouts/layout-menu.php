<?php

use yii\helpers\Url;
use yii\helpers\Html;
use admin\widgets\Icon;
?>
<div class="page-sidebar menu-compact" id="sidebar">
    <!-- Page Sidebar Header-->
    <div class="sidebar-header-wrapper">
        <?= Icon::fa('home');  ?>
        <input type="text" class="searchinput" />
        <i class="searchicon fa fa-search"></i>
        <div class="searchhelper">Search Reports, Charts, Emails or Notifications</div>
    </div>
    <!-- /Page Sidebar Header -->
    <!-- Sidebar Menu -->
    <ul class="nav sidebar-menu">
        <!--Dashboard-->
        <li>
            <?= Html::a(Icon::glyph('home'). Html::tag('span', '&nbsp; Dashboard', ['class'=>'menu-text']), ['/administrator']) ?>
        </li>
        <li>
            <?= Html::a(Icon::glyph('home'). Html::tag('span', '&nbsp; Dashboard', ['class'=>'menu-text']), ['/administrator']) ?>
        </li>
    </ul>
</div>
