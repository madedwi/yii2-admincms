<?php
namespace admin\bootstraps;

use Yii;

class AdminBootstrap implements \yii\base\BootstrapInterface{
    public function bootstrap($app){
        $rules = [
            //-------------------------------------------------------------------------------------------------------------------------------
            '<module:administrator>/login' => '<module>/dashboard/login',
            '<module:administrator>/logout' => '<module>/dashboard/logout',

            '<module:administrator>/content/<controller:(page|post|category|comment)>/<id:\d+>/<action>' => '<module>/<controller>/<action>',
            '<module:administrator>/content/<controller:(page|post|category|comment)>/<action>' => '<module>/<controller>/<action>',
            '<module:administrator>/content/<controller:(page|post|category|comment)>' => '<module>/<controller>/index',
        ];

        if(file_exists(Yii::getAlias('@runtime/router.json'))){
            $jsonString = file_get_contents(Yii::getAlias('@runtime/router.json'));
            $routerArray = json_decode($jsonString, true);
            $rules = array_merge($rules, $routerArray);
        }

        $app->getUrlManager()->addRules($rules, TRUE);
        // $app->params = array_merge($app->params, require_once(Yii::getAlias('@themes/basic/config.php')));
    }
}
