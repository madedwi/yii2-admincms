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
        // Yii::$app->setHomeUrl(Yii::getAlias('@adminUrl'));
        // $this->setComponents();
        parent::init();


        $postSettings = array_merge([
            'post_have_category' => true,
            'post_have_tags' => true,
        ], ArrayHelper::getValue(Yii::$app->params, 'post_settings'));

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
    }
}
