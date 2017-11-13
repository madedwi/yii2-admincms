<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use admin\assets\AdminAsset;
use admin\components\AdminTemplate;
use admin\widgets\Icon;

$assetBundle = AdminAsset::register($this);
$template = new AdminTemplate($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title>Administrator <?= !empty($this->title) ? ' - ' . Html::encode($this->title) : '' ?></title>
    <?php $this->head() ?>
    <link rel="icon" href="<?= $assetBundle->baseUrl . '/img/favicon.png'?>" />
    <script type="text/javascript">
    var admin = {
        baseUrl : '<?= Yii::getAlias('@adminUrl') ?>'
    };
    </script>
</head>
<body>
<?php $this->beginBody() ?>
    <!-- TOP NAVBAR -->
    <div class="navbar">
        <div class="navbar-inner">
            <div class="navbar-container">
                <!-- Navbar Barnd -->
                <div class="navbar-header pull-left" style="padding-left:60px;">
                    <a href="#" class="navbar-brand">
                        <?= Html::img(Yii::$app->params['header_logo_image'], ['class'=>'logo', 'height'=>'100%'])?>
                    </a>
                </div>
                <!-- /Navbar Barnd -->
                <!-- Sidebar Collapse -->
                <div class="sidebar-collapse" id="sidebar-collapse">
                    <i class="collapse-icon fa fa-bars"></i>
                </div>
                <!-- /Sidebar Collapse -->
                <!-- Account Area and Settings --->
                <div class="navbar-header pull-right">
                    <div class="navbar-account">
                        <ul class="account-area">
                            <li>
                                <a class="login-area dropdown-toggle" data-toggle="dropdown">
                                    <div class="avatar" title="View your public profile">
                                        <?php //Html::img($assetBundle->baseUrl . '/img/avatars/adam-jansen.jpg')?>
                                    </div>
                                    <section>
                                        <h2><span class="profile"><span><?= Yii::$app->user->identity->firstname ?></span></span></h2>
                                    </section>
                                </a>
                                <!--Login Area Dropdown-->
                                <ul class="pull-right dropdown-menu dropdown-arrow dropdown-login-area">
                                    <!--/Theme Selector Area-->
                                    <li class="dropdown-footer">
                                        <?= Html::a('logout', ['/administrator/logout'], [
                                                'data'=>[
                                                    'method' => 'post',
                                                ]
                                            ])?>
                                    </li>
                                </ul>
                                <!--/Login Area Dropdown-->
                            </li>
                        </ul>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END TOP NAVBAR -->

    <!-- MAIN CONTAINER -->
    <div class="main-container container-fluid">
        <!-- Page Container -->
        <div class="page-container">
            <?= $this->render('menu') ?>
        </div>

        <!-- Page Content -->
        <div class="page-content">
            <!-- Page Breadcrumb -->
            <div class="page-breadcrumbs">
                <?= Breadcrumbs::widget([
                    'homeLink' => [
                                'label' => 'Home',  // required
                                'url' => ['site'],      // optional, will be processed by Url::to()
                                'template' => Html::tag('li', Icon::FA('home') . ' {link} ')
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
            </div>
            <!-- /Page Breadcrumb -->
            <!-- Page Header -->
            <div class="page-header position-relative">
                <div class="header-title">
                    <h1>

                        <?= $this->title ?>
                    </h1>
                </div>
                <!--Header Buttons-->
                <div class="header-buttons">
                    <a class="sidebar-toggler" href="#">
                        <i class="fa fa-arrows-h"></i>
                    </a>
                    <a class="refresh" id="refresh-toggler" href="">
                        <i class="glyphicon glyphicon-refresh"></i>
                    </a>
                    <a class="fullscreen" id="fullscreen-toggler" href="#">
                        <i class="glyphicon glyphicon-fullscreen"></i>
                    </a>
                </div>
                <!--Header Buttons End-->
            </div>
            <!-- /Page Header -->
            <!-- Page Body -->
            <div class="page-body">
                <?= $content ?>
            </div>
        </div>
    </div>
    <!-- END MAIN CONTAINER -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
