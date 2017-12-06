<?php
namespace admin\components;

class Model extends \yii\base\Model{
    private $_custom_attributes = [];

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

    public function customAttributes(){
        return [];
    }

    public function customAttributeLabels(){
        return [];
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


    public function toArray($fields = [], $expands = [], $recursive = true){
        $array = parent::toArray($fields, $expands, $recursive);

        foreach ($this->customAttributes as $name => $value) {
            $array[$name] = $value;
        }

        return $array;
    }
}
