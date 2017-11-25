<?php
namespace admin\models;

class FrontedMenu extends Options {

    public function customAttributes(){
        return ['menu_text', 'url', 'element_id', 'element_class'];
    }

    public function rules(){
        return [
            [['menu_text'], 'string', 'max'=>25, 'min'=>3],
            [['url'], 'string'],
            [['element_id', 'element_class'], 'string', 'max'=>150]
        ];
    }

    
}
