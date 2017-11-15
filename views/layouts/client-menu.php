<?php
/**
 * @property $mainUrl;
 * @property $subUrl;
 **/


 use yii\helpers\Url;
 use yii\helpers\Html;
 use admin\widgets\Icon;

if(!empty($cms)){
    foreach ($cms as $menuID => $menuInfo) {
        if($menuInfo['parent'] !== false){
            continue;
        }
        $class = $menuInfo['class'];
        if(in_array($mainUrl, $menuInfo['activeIf'])){
            $class .= 'open';
        }

        $html  = Html::beginTag('li', ['class'=>$class]);
        if(array_key_exists('childs', $menuInfo) && count($menuInfo['childs']) > 0){
            $html .= Html::a($menuInfo['icon']. Html::tag('span', '&nbsp; '. $menuInfo['label'], ['class'=>'menu-text']), 'javascript:', ['class'=>'menu-dropdown']);
            $html .= Html::beginTag('ul', ['class'=>'submenu']);
            foreach ($menuInfo['childs'] as $key => $menuChild) {
                $isActive = false;
                if($mainUrl == $menuChild['activeIf']['mainUrl'] && in_array($subUrl, $menuChild['activeIf']['subUrl'])){
                    $isActive = true;
                }

                $html .= Html::tag('li', Html::a(Html::tag('span', $menuChild['label'], ['class'=>'menu-text ' . $menuChild['class']]), $menuChild['url']), ['class'=>$isActive ? 'active' : '']);
            }
            $html .= Html::endTag('ul');
        }
        $html .= Html::endTag('li');

        echo $html;
    }
}
?>
