<?php
namespace admin\models;

use Yii;

class GeneralOptions extends Options{

    public $web_title, $web_tagline, $favicon;
    public $web_meta_description, $web_meta_keyword;
    public $admin_email;

    public $timezone, $date_format, $time_format;
    public $post_url_format;
    public $lastModified;

    public $header_logo_image;

    public $custom_metas;

    private $loadedOptions;

    private static $otherCustomMetas;

    // social media
    public $facebook, $twitter, $gplus, $instagram, $pinterest;

    public function init(){
        $this->custom_metas = [];
        if(isset(Yii::$app->params['client_options'])){
            foreach (Yii::$app->params['client_options'] as $metaGroup) {
                $input_meta = isset($metaGroup['meta_input']) ? $metaGroup['meta_input'] : [];
                foreach ($input_meta as $key => $value) {
                    $this->custom_metas[$key] = "";
                }
            }
        }

        $cacheDepedency = new \yii\caching\DbDependency(['sql'=>"SELECT option_key, option_value FROM ".self::tableName()." WHERE option_key='lastModified' LIMIT 1;"]);
        $this->loadedOptions  = [
            'web_title', 'web_tagline', 'favicon', 'web_meta_description', 'web_meta_keyword', 'timezone', 'time_format',
            'date_format', 'admin_email', 'post_url_format', 'header_logo_image',
            'facebook', 'twitter', 'gplus', 'instagram', 'pinterest'];

        $loadedOptions = array_merge($this->loadedOptions, array_keys($this->custom_metas));
        $options = Yii::$app->cache->getOrSet('general_option_values', function()use($loadedOptions){
            return parent::find()->where(['option_key'=>$loadedOptions])->asArray()->all();
        }, (3600*24*7), $cacheDepedency);

        foreach ($options as $option) {
            if(array_key_exists($option['option_key'], $this->custom_metas)){
                $this->custom_metas[$option['option_key']] = $option['option_value'];
            }else{
                $this->$option['option_key'] = $option['option_value'];
            }

        }

        parent::init();
    }

    public function rules(){
        return [
            [['web_title'], 'required'],
            [['web_title', 'web_tagline', 'post_url_format'], 'string', 'max'=>'70'],
            [['web_meta_description', 'web_meta_keyword'], 'string', 'max'=>120],
            [['favicon', 'header_logo_image'], 'string'],
            [['timezone'], 'in', 'range'=>$this->supportedTimeZone],
            [['date_format', 'time_format'], 'string', 'max'=>15],
            [['admin_email'],  'email'],
            [['custom_metas', 'lastModified'], 'safe'],
            [['facebook', 'twitter', 'gplus', 'instagram', 'pinterest'], 'string'],

        ];
    }

    public function getSupportedTimeZone(){
        return [
            'UTC'=>'UTC',
            'ASIA/MAKASAR'=>'ASIA/MAKASAR',
            'ASIA/JAKARTA'=>'ASIA/JAKARTA'
        ];
    }

    public function save($runValidation = true, $attributeNames = null ){
        if($this->validate()){
            $this->insertOptions('web_title', $this->web_title);
            $this->insertOptions('web_tagline', $this->web_tagline);
            $this->insertOptions('favicon', $this->favicon);
            $this->insertOptions('web_meta_description', $this->web_meta_description);
            $this->insertOptions('web_meta_keyword', $this->web_meta_keyword);
            $this->insertOptions('timezone', $this->timezone);
            $this->insertOptions('date_format', $this->date_format);
            $this->insertOptions('time_format', $this->time_format);
            $this->insertOptions('admin_email', $this->admin_email);
            $this->insertOptions('post_url_format', $this->post_url_format);
            $this->insertOptions('header_logo_image', $this->header_logo_image);
            $this->insertOptions('lastModified', date('Y-m-d H:i:s'));

            $this->insertOptions('facebook', $this->facebook);
            $this->insertOptions('twitter', $this->twitter);
            $this->insertOptions('instagram', $this->instagram);
            $this->insertOptions('gplus', $this->gplus);
            $this->insertOptions('pinterest', $this->pinterest);


            $this->generatePostUrlRoute();


            foreach ($this->custom_metas as $key => $value) {
                $this->insertOptions($key, $value);
            }

            return true;
        }

        return false;
    }

    public function generatePostUrlRoute(){
        // load route json file;
        $routeExp = strtr($this->post_url_format, [
            '{[' => '<',
            ']}' => '>',
            'publish_year' => 'year:\d+',
            'publish_month_numeric' => 'month:\d+',
            'publish_month_name' => 'monthname:\w+',
            'category' => 'terms:[\w\-]+',
        ]);

        $defaultController = Yii::$app->controller->module->defaultClientRoute;
        $defaultController = str_replace('controller', '', strtolower($defaultController));
        $routeArray = [
            '' =>  $defaultController. '/index',
            "{$routeExp}" => $defaultController . '/post',
            '<slug:[\w\-]+>' => $defaultController . '/page',
            'archives/<category:[\w\-]+>' => $defaultController . '/archives',
            'archives/<year:\d+>/<month:\d+>' => $defaultController . '/archives',
            'archives/<year:\d+>' => $defaultController . '/archives'
        ];

        $fp = fopen(Yii::getAlias('@runtime/router.json'), 'w');
        fwrite($fp, json_encode($routeArray));
        fclose($fp);
    }

    public function loadClientOptions($options = []){
        $optionKey = [];
        if(isset($options) && !empty($options)){
            foreach ($options as $metaGroup) {
                $input_meta = isset($metaGroup['meta_input']) ? $metaGroup['meta_input'] : [];
                foreach ($input_meta as $key => $value) {
                    if($value['format'] == 'group_input'){
                        foreach ($value['inputs'] as $key => $value) {
                            $optionKey[] = $key;
                            $this->custom_metas[$key] = "";
                        }
                    }else{
                        $optionKey[] = $key;
                        $this->custom_metas[$key] = "";
                    }

                }
            }
        }

        $_opt = self::find()->where(['option_key'=>$optionKey])->asArray()->all();
        $opts = \yii\helpers\ArrayHelper::map($_opt, 'option_key', 'option_value');
        $this->custom_metas = array_merge($this->custom_metas, $opts);
    }

    public function getLoadedOptions(){
        return $this->loadedOptions;
    }

}
