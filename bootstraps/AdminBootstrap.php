<?php
namespace admin\bootstraps;

use Yii;
use admin\models\GeneralOptions;

class AdminBootstrap implements \yii\base\BootstrapInterface{
    public function bootstrap($app){
        // date_default_timezone_set('UTC');

        $rules = [
            //-------------------------------------------------------------------------------------------------------------------------------
            '<module:administrator>' => '<module>/dashboard/index',
            '<module:administrator>/login' => '<module>/dashboard/login',
            '<module:administrator>/logout' => '<module>/dashboard/logout',

            '<module:administrator>/content/<controller:(page|post|category|comment)>/<id:\d+>/<action>' => '<module>/<controller>/<action>',
            '<module:administrator>/content/<controller:(page|post|category|comment)>/<action>' => '<module>/<controller>/<action>',
            '<module:administrator>/content/<controller:(page|post|category|comment)>' => '<module>/<controller>/index',
        ];

        if(file_exists(Yii::getAlias('@runtime/router.json'))){
            $jsonString = file_get_contents(Yii::getAlias('@runtime/router.json'));
            $routerArray = json_decode($jsonString, true);
            // $rules = array_merge($rules, $routerArray);
            foreach ($routerArray as $key => $value) {
                $rules[$key] = $value;
            }
        }

        $app->set('_adminFileCache', 'yii\caching\FileCache');

        $app->set('registerMetaTag', 'admin\components\Meta');

        $app->set('wimaraDateTime', 'admin\components\DateTime');

        $app->getUrlManager()->addRules($rules, TRUE);
        // $app->params = array_merge($app->params, require_once(Yii::getAlias('@themes/basic/config.php')));

        $genOptions = new GeneralOptions();
        foreach ($genOptions->loadedOptions as $opt) {
            $app->params[$opt] = $genOptions->$opt;
        }
    }
}
