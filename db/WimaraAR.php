<?php
namespace admin\db;

class WimaraAR extends \yii\db\ActiveRecord{

    private $_custom_attributes = [];

    static $instance;

    public function __construct(){
        parent::__construct();

        foreach ($this->customAttributes() as $attributes) {
            $this->setCustomAttribute($attributes, null);
        }

        parent::init();
    }

    public function __set($name, $value) {
        if(array_key_exists($name, $this->_custom_attributes)){
            $this->_custom_attributes[$name] = $value;
            return;
        }
        return parent::__set($name, $value);
    }

    public function __get($name){
        if(array_key_exists($name ,$this->_custom_attributes)){
            return $this->_custom_attributes[$name];
        }

        return parent::__get($name);
    }

    public function __isset($name)
    {
        if(array_key_exists($name, $this->_custom_attributes)){
            return !is_null($this->_custom_attributes[$name]);
        }

        return parent::__isset($name);
    }

    protected function getQuery(){
        return new \yii\db\Query();
    }

    protected function getCache(){
        return Yii::$app->cache;
    }

    public function customAttributes(){
        return [];
    }

    public function customAttributeLabels(){
        return [];
    }

    public function attributeLabels(){
        return $this->customAttributeLabels();
    }

    public function hasCustomAttribute($name){
        return isset($this->_custom_attributes[$name]) || in_array($name, $this->customAttributes(), true);
    }

    public function setCustomAttribute($name, $value){
        $this->_custom_attributes[$name] = $value;
    }

    public function getCustomAttribute($name){
        return $this->_custom_attributes[$name];
    }

    public function getCustomAttributes(){
        return $this->_custom_attributes;
    }


    public function toArray($obj = NUll){
        $array = [];
        if(is_null($obj)){
            $obj = $this;
        }

        foreach ($obj as $key => $value) {
            if($value instanceof WimaraAR){
                $value = $value->toArray();
            }else if($value instanceof \yii\db\ActiveDataProvider){
                $value = $this->toArray($value->models);
            }else if(is_array($value)){
                $value = $this->toArray($value);
            }else if(is_object($value)){
                $value = $this->toArray($value);
            }
            $array[$key] = $value;
        }
        if(isset($obj->customAttributes)){
            foreach ($obj->customAttributes as $key => $value) {
                if($value instanceof WimaraAR){
                    $value = $value->toArray();
                }else if($value instanceof \yii\db\ActiveDataProvider){
                    $value = $this->toArray($value->models);
                }else if(is_array($value)){
                    $value = $this->toArray($value);
                }else if(is_object($value)){
                    $value = $this->toArray($value);
                }
                $array[$key] = $value;
            }
        }

        return $array;

    }



}
