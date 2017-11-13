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
                <div class="navbar-header pull-left">
                    <a href="#" class="navbar-brand">

                        ini logo
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
                                        <?= Html::img($assetBundle->baseUrl . '/img/avatars/adam-jansen.jpg')?>
                                    </div>
                                    <section>
                                        <h2><span class="profile"><span>David Stevenson</span></span></h2>
                                    </section>
                                </a>
                                <!--Login Area Dropdown-->
                                <ul class="pull-right dropdown-menu dropdown-arrow dropdown-login-area">
                                    <!--/Theme Selector Area-->
                                    <li class="dropdown-footer">
                                        <a href="login.html">
                                            Sign out
                                        </a>
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
            <?= $this->render('layout-menu') ?>
        </div>

        <!-- Page Content -->
        <div class="page-content">
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
