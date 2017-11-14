<?php

namespace admin;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * administrator module definition class
 */
class Administrator extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'admin\controllers';
    public $viewPath    = '@admin/views';
    public $layoutPath  = '@admin/views/layouts';
    public $layout      = 'main';
    public $defaultRoute = 'dashboard';

    /**
     * @inheritdoc
     */
    public function init()
    {
        Yii::setAlias('@adminUrl', Yii::getAlias('@web/administrator'));
        parent::init();


        $postSettings = array_merge([
            'post_have_category' => true,
            'post_have_tags' => true,
        ], ArrayHelper::getValue(Yii::$app->params, 'post_settings', []));

        Yii::configure($this, [
            'components' => [
                'activity' => [
                    'class' => 'admin\components\ActivityLog'
                ],
                'dateTime' => [
                    'class' => 'admin\components\DateTime'
                ],
                'media' => [
                    'class' => 'admin\components\Media'
                ]
            ],
            'params' => [
                'image_sizes' => [
                    'large'  => [1200, 1200],
                    'medium' => [480, 480],
                    'small'  => [150, 150],
                ],
                'default_user_type' => [
                    'root' => 'Root',
                    'administrator' => 'Administrator'
                ],
                'post_settings' => $postSettings
            ]
        ]);
        $adminOptions = array_merge([
                'header_logo_image' => '',
                'registeredAdminMenus' => ArrayHelper::getValue(Yii::$app->params, 'registeredAdminMenus', [])
            ], $this->getWebOptions());
        Yii::$app->params = array_merge(Yii::$app->params, $adminOptions);
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
