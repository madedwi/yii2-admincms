<?php
use admin\components\AdminTemplate;

$this->title = "User Access Control";
$template = new AdminTemplate($this);
?>

<div class="row">
    <div class="col-xs-12 col-md-12">
        <?php
        $template->widgetBegin()
                ->widgetHeader([
                    'title' => 'Access Control',
                    'icon' => 'list-alt',
                    'iconType' => 'glyph',
                    // 'buttons' => [
                    //     Html::a(Icon::FA('plus') . 'Tambah Baru', ['create'], ['class'=>'btn btn-sm btn-success btn-new-form'])
                    // ]
                ])
                ->widgetBody(['excludeCloseTag'=>true]);
        ?>
        <div class="alert alert-warning">Underconstruction!</div>
        <?php $template->widgetEnd()?>
    </div>
</div>
