<?php
namespace admin\components;

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


        Yii::$app->params = array_merge(Yii::$app->params, $this->getWebOptions());

        // $app->params = array_merge($app->params, require_once(Yii::getAlias('@themes/basic/config.php')));
    }

    private function getWebOptions(){
        $query = new \yii\db\Query();
        $q = clone $query;
        $dependency = new \yii\caching\DbDependency([
            'sql' => $q->select('option_value')->from('web_options')->where(['option_key'=>'lastModified'])->limit(1)->createCommand()->rawSql
        ]);
        return Yii::$app->cache->getOrSet('web_options', function()use($query){
            $q = clone $query;
            $_queryresult = $q->select('*')->from('web_options')->all();
            return \yii\helpers\ArrayHelper::map($_queryresult, 'option_key', 'option_value');
        }, null, $dependency);


    }
}
