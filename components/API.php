<?php
namespace admin\components;

use Yii;
use yii\base\Component;
use yii\data\ActiveDataProvider;

class API extends Component {

    public static $instances = array();

    public static function getInstance(){
        $class = get_called_class();
        if(array_key_exists($class, API::$instances)){
            return API::$instances[$class];
        }

        return API::$instances[$class] = new $class();
    }

}
