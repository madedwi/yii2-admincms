<?php
use yii\helpers\Html;

\admin\assets\MediaManagerAsset::register(Yii::$app->view);
$baseUrlAsset = \admin\assets\AdminAsset::register($this);

if(!function_exists('registerCustomMediaMedia')){
    function registerCustomMediaMedia($_this){
        if(!array_key_exists('registered_custom_media_manager_meta', Yii::$app->params)){
            Yii::$app->params['registered_custom_media_manager_meta'] = true;
            $script = <<<JAVASCRIPT

            $(document).on('click', '.btn-add-multiple_media', function(e){
                let med;
                if(typeof med == 'undefined'){
                    med = MediaManager();
                    med.init({
                        controllerUrl : admin.baseUrl +"/media/index",
                        basePath     : 'uploads/images'
                    });
                }

                var mm_container    = $(this).closest('.mm-container'),
                    nextContainer  = $('.mm-container').length + 1,
                    htmlNextContainer = '<div class="row mm-container" data-counter="'+nextContainer+'">' + mm_container.html().trim() + '</div>',
                    containerParent = mm_container.parent('div');
                if(typeof med != 'undefined'){
                    med.open(function(image){
                        mm_container.find('.multiple_media-filename').val(image.fileurl);
                        mm_container.find('.mm-image-preview').attr('src', image.fileurl);
                        if(mm_container.data('single-image')!==true){
                            containerParent.append(htmlNextContainer);
                            let newContainer = $(document).find('.mm-container[data-counter='+nextContainer+']');
                            newContainer.find('.multiple_media-filename').attr('name', 'multiple_media['+nextContainer+'][filename]');
                            newContainer.find('.multiple_media-title').attr('name', 'multiple_media['+nextContainer+'][title]');
                            newContainer.find('.multiple_media-description').attr('name', 'multiple_media['+nextContainer+'][description]');
                        }
                    });
                }else{
                    bootbox.alert('Media manager not initialize.');
                }

            });
JAVASCRIPT;
            $_this->registerJs($script);
        }
    }
}

foreach ($inputs as $meta_key=>$input) {
    $tagValue = isset($model->$meta_key) ? $model->$meta_key : '';
    $options = isset($input['options']) ? $input['options'] : [];
    $hint = array_key_exists('hint', $options) ? $options['hint'] : "";
    unset($options['hint']);

    if($input['format'] == 'group_input'){
        echo Html::tag('h5', $input['label']);
        echo $this->render('custom_meta_input',[
            'model' => $model,
            'form' => $form,
            'inputs' => $input['inputs']
        ]);
        echo Html::tag('hr');
    }else if($input['format'] == 'texteditor'){
        \admin\assets\EditorAsset::register(Yii::$app->view);
        $options = array_merge(['value'=>$tagValue, 'rows'=>3, 'class'=>'form-control'], $options);
        $options['class'] .= ' custom-meta_summernote ';
        echo $form->field($model, $meta_key)->textarea($options)->label($input['label'])->hint($hint);
        $this->registerJs("$('.custom-meta_summernote').summernote({height:300})", \yii\web\View::POS_READY, 'summernote-custom-meta');
    }else if($input['format'] == 'shorttext'){
        $options = array_merge(['value'=>$tagValue, 'maxlength'=>200], $options);
        echo $form->field($model, $meta_key)->textInput($options)->label($input['label'])->hint($hint);
    }else if($input['format'] == 'select'){
        $options = array_merge(['value'=>$tagValue], $options);
        echo $form->field($model, $meta_key)->dropDownList($input['values'], $options)->label($input['label'])->hint($hint);
    }else if($input['format'] == 'checkbox'){
        $checked = false;
        if(($tagValue==$input['value'])){
            $checked = true;
        }
        $options = array_merge(['value'=>$input['value'],'checked'=> $checked, 'label'=>Html::tag('span', $input['label'], ['class'=>'text'])], $options);
        echo $form->field($model, $meta_key)->checkbox($options)->hint($hint);
    }else if($input['format'] == 'datepicker'){
        $options = array_merge(['value'=>$tagValue, 'class'=>'form-control', 'data-targetinput'=>'#date_'.$meta_key], $options);
        $options['class'] .= " bs-selectdate";
        echo  $form->field($model, $meta_key)->textInput($options)->label($input['label'])->hint($hint);
        echo Html::activeHiddenInput($model, $meta_key, ['id'=>'date_'.$meta_key]);
    }else if($input['format'] == 'media'){
        $filename = $model->custom_metas[$meta_key];
        // $filename = $savedData['filename'];
        // $title = $savedData['filename'];
        ?>
        <div class="form-group mm-container" data-single-image="true">

            <div class="clearfix">
                <?= Html::activeHiddenInput($model, $meta_key,['class'=>'multiple_media-filename'])?>
                <button type="button" class="btn btn-default btn-add-multiple_media">Add <?= $input['label'] ?></button>
                <p></p>
            </div>
            <div class="clearfix">
                <div class="thumbnail text-center" style="max-height:150px;">
                    <?= Html::img(empty($filename) ? "{$baseUrlAsset->baseUrl}/img/placeholder.jpg" : $filename, ['class'=>'mm-image-preview img-responsive', 'style'=>'max-height:140px;']) ?>
                </div>
            </div>
        </div>
        <?php
        registerCustomMediaMedia($this);
    }else if($input['format'] == 'multiple_media'){
        $savedData = $model->custom_metas[$meta_key];
        if(!empty($savedData)){
            $medias = json_decode($savedData, true);
        }else{
            $medias = [];
        }
        // print_r(count($medias));

        $i = 1;
        do{
            $filename     = isset($medias[$i]['filename']) ? $medias[$i]['filename'] : '' ;
            // $filename   = isset($medias->$i->filename) ? $medias->$i->filename : '';
            $title      = isset($medias[$i]['title']) ? $medias[$i]['title'] : '';
            $description = isset($medias[$i]['description']) ? $medias[$i]['description'] : '';
            ?>
            <div class="row mm-container" data-counter="1">
                <div class="col-md-3">
                    <div class="thumbnail" style="max-heigth:150px; max-width:150px;">
                        <?= Html::img(empty($filename) ? "{$baseUrlAsset->baseUrl}/img/placeholder.jpg" : $filename, ['class'=>'mm-image-preview']) ?>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="form-group">
                        <?= Html::hiddenInput('multiple_media['.$i.'][filename]', $filename, ['class'=>'multiple_media-filename'])?>
                        <?= Html::textInput('multiple_media['.$i.'][title]', $title, ['class'=>'form-control multiple_media-title', 'placeholder'=>'Media title'])?>
                    </div>
                    <div class="form-group">
                        <?= Html::textarea('multiple_media['.$i.'][description]', $description, ['class'=>'form-control multiple_media-description', 'placeholder'=>'Media description'])?>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-default btn-add-multiple_media">Add Media</button>
                    </div>
                </div>
            </div>
            <?php
            $i++;
        } while($i <= count($medias));

        registerCustomMediaMedia($this);
    }
}
