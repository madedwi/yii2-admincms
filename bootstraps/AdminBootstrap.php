<?php
namespace admin\bootstraps;

use Yii;

class AdminBootstrap implements \yii\base\BootstrapInterface{
    public function bootstrap($app){
        $app->getUrlManager()->addRules([
            //-------------------------------------------------------------------------------------------------------------------------------
            '<module:administrator>/login' => '<module>/dashboard/login',
            '<module:administrator>/logout' => '<module>/dashboard/logout',

            '<module:administrator>/content/<controller:(page|post|category|comment)>/<id:\d+>/<action>' => '<module>/<controller>/<action>',
            '<module:administrator>/content/<controller:(page|post|category|comment)>/<action>' => '<module>/<controller>/<action>',
            '<module:administrator>/content/<controller:(page|post|category|comment)>' => '<module>/<controller>/index',
        ], TRUE);


        

        // $app->params = array_merge($app->params, require_once(Yii::getAlias('@themes/basic/config.php')));
    }
}
