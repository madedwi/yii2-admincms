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

AdminAsset::register($this);
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
    <script type="text/javascript">
    var admin = {
        baseUrl : '<?= Yii::getAlias('@adminUrl') ?>'
    };
    </script>
</head>
<body>
<?php $this->beginBody() ?>
    <?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
