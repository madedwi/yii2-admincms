<?php
namespace admin\components;

use Yii;
use admin\assets\AdminAsset;
use admin\widgets\Icon;


class AdminTemplate extends \yii\helpers\Html{

    private $widget;
    private $widgetOptions;
    private $assets;

    public $header;

    public function __construct($view){
        $this->assets = AdminAsset::register($view);
    }

    public function registerJs($js){
        $this->assets->js[] = $js;
    }

    public function registerCss($css){
        $this->assets->css[] = $css;
    }

    private static function renderIcon($type, $icon){
        switch ($type) {
            case 'FA':
                    $icon = str_replace('fa-', '', strtolower($icon));
                    return Icon::FA($icon);
                break;
            case 'glyph' :
                    $icon = str_replace('glyphicon-', '', strtolower($icon));
                    return Icon::glyph($icon);
                break;
            case 'typicon' :
                    $icon = str_replace('typcn-', '', strtolower($icon));
                    return Icon::typicon($icon);
                break;
            default:
                    return '';
                break;
        }
    }

    public function widgetBegin(){

        echo $this->widget = parent::beginTag('div', ['class'=>'widget']);
        return $this;
    }

    public function widgetHeader($option = []){
        if(!empty($this->widget)){
            $def = [
                'options' => ['class'=>'widget-header'],
                'title' => '',
                'icon' => '',
                'iconType' => 'FA',
                'buttons' => []
            ];

            $def = array_merge($def, $option);

            $header = parent::beginTag('div', $def['options']);
            $header .= parent::beginTag('span', ['class'=>'widget-caption']);
            if(!empty($def['icon'])){
                $header .= self::renderIcon($def['iconType'], $def['icon']) . '&nbsp; ';
            }
            $header .= $def['title'];
            $header .= parent::endTag('span');

            $buttons = '';
            if(!is_array($def['buttons'])){
                $buttons = $def['buttons'];
            }else if(is_array($def['buttons'])){
                foreach($def['buttons'] as $button){
                    if(!is_array($button)){
                        $buttons .= $button;
                    }else{
                        $bdef = [
                            'label' => '',
                            'options' => '',
                            'icon' => '',
                            'iconType' => 'FA',
                        ];

                        $bdef = array_merge($bdef, $button);
                        if(!empty($bdef['icon'])){
                            $bdef['label'] = self::renderIcon($bdef['iconType'], $bdef['icon'] ) . '&nbsp; '. $bdef['label'];
                        }
                        $buttons .= parent::button($bdef['label'], $bdef['options']);
                    }
                }
            }

            if(!empty($buttons)){
                $header .= parent::tag('div',
                                        parent::tag('div', $buttons, ['class'=>'btn-group']),
                                        ['class'=>'widget-buttons widget-input']);
            }

            $header .= parent::endTag('div');
            echo $header;
        }
        return $this;
    }

    public function widgetBody($option = []){
        $def = [
            'content' => '',
            'options' => ['class'=>'widget-body'],
            'excludeCloseTag' => false
        ];

        if(!empty($this->widget)){

            $def = array_merge($def, $option);

            $body = parent::beginTag('div', $def['options']);
            $body .= $def['content'];

            if($def['excludeCloseTag'] == false){
                $body .= parent::endTag('div');
                $this->widgetOptions['useBody'] = false;
            }else{
                $this->widgetOptions['useBody'] = true;
            }

            echo $body;
        }

        return $this;
    }

    public function widgetEnd(){
        if(isset($this->widgetOptions['useBody']) && $this->widgetOptions['useBody']==true){
            echo parent::endTag('div'); // close body
        }

        echo parent::endTag('div'); // close widget
    }

    public static function getInstance($view){
        $_template = new AdminTemplate($view);
        return $_template;
    }


    public function renderAlert($flashKeyword){
        $alert = "";
        if(Yii::$app->session->hasFlash($flashKeyword)){
            $flashData = Yii::$app->session->getFlash($flashKeyword);
            if($flashData['status']=='warning'){
                $icon           = "exclamation";
                $alert_class    = "alert-warning";
            }else if($flashData['status']=='info'){
                $icon           = "info";
                $alert_class    = "alert-info";
            }else if($flashData['status']=='error'){
                $icon           = "ban";
                $alert_class    = "alert-danger";
            }else{
                $icon           = "check";
                $alert_class    = "alert-success";
            }

            $alert  = parent::tag('div',
                    Icon::FA($icon) . ' &nbsp; ' .
                    parent::tag('span', $flashData['message'], ['class'=>'alert-text', 'style'=>'margin-left:5px;']),
                    ['class'=>"alert {$alert_class}" ]
                );
        }

        return $alert;
    }

}
