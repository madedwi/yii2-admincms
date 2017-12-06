<?php
use yii\helpers\Html;
?>

<div class="form-group">
    <label for="slug" class="control-label">Pretty Url</label>
    <p>
        <?php

        $slugformat = empty($slugformat) ? '{[slug]}' : $slugformat;
        $expl   = explode('/', $slugformat);
        $_expl  = [];
        $_i     = 0;
        $slug_pos = 0;
        foreach ($expl as $i=>$x) {
            if(strpos($x, '-') !== false){
                $_x = explode('-', $x);
                foreach ($_x as $_xi) {
                    $_expl[$_i] = $_xi;
                    if(strpos($x, '{[slug]}')!== false){
                        $slug_pos = $_i;
                    }
                    $_i++;
                }
                $_i--;
            }else{
                $_expl[$_i] = $x;
                if(strpos($x, '{[slug]}')!== false){
                    $slug_pos = $_i;
                }
            }
            $_i ++;
        }

        $expl = $_expl;
        // $text = [];
        $cx = [];


        $slugSegment =[]; //explode('/', $model->formattedSlug);
        $currentSegment = 0;
        $fullSlug = $slugformat;
        foreach ($expl as $slugKeyword) {
            $c = "";
            if($slugKeyword=="{[category]}"){
                if(isset($slugSegment[$currentSegment]) && in_array($slugSegment[$currentSegment], $slugCategories)){
                    $textCategorySlug = $slugSegment[$currentSegment];
                }if(count($slugCategories)==1){
                    $textCategorySlug = array_keys($slugCategories)[0];
                }else{
                    $textCategorySlug = '{[categories]}';
                }

                $c = Html::tag('span',
                            Html::tag('span', $textCategorySlug, ['class'=>'slug-click slug-segment', 'data'=>['input'=>'#slug-category']]) .
                            Html::dropDownList('slug-category', null, $slugCategories, ['class'=>'slug-input hidden', 'id'=>'slug-category'])
                        );

                $fullSlug = str_replace('{[category]}', $c, $fullSlug);
                $cx[] = $textCategorySlug;
            }else if(strtolower($slugKeyword)=="{[publish_year]}"){
                if(!empty($model->publishdate)){
                    $textYearSlug = date('Y', strtotime($model->publishdate));
                }else{
                    $textYearSlug = isset($slugSegment[$currentSegment]) && !empty($slugSegment[$currentSegment]) ? $slugSegment[$currentSegment] : date('Y');
                }
                $c = Html::tag('span', $textYearSlug, ['class'=>'slug-publishyear slug-segment']);
                $fullSlug = str_replace('{[publish_year]}', $c, $fullSlug);
                $cx[] = $textYearSlug;
            }else if(strtolower($slugKeyword)=="{[publish_month_numeric]}"){
                if(!empty($model->publishdate)){
                    $textMonthSlug = date('m', strtotime($model->publishdate));
                }else{
                    $textMonthSlug = isset($slugSegment[$currentSegment]) && !empty($slugSegment[$currentSegment]) ? $slugSegment[$currentSegment] : date('m');
                }

                $c = Html::tag('span', $textMonthSlug, ['class'=>'slug-publishmonthnumeric slug-segment']);
                $fullSlug = str_replace('{[publish_month_numeric]}', $c, $fullSlug);
                $cx[] = $textMonthSlug;
            }else if(strtolower($slugKeyword)=="{[publish_month_name]}"){
                if(!empty($model->publishdate)){
                    $textMonthNameSlug = date('F', strtotime($model->publishdate));
                }else{
                    $textMonthNameSlug = isset($slugSegment[$currentSegment]) && !empty($slugSegment[$currentSegment]) ? $slugSegment[$currentSegment] : date('F');
                }
                $textMonthNameSlug = strtolower($textMonthNameSlug);
                $c = Html::tag('span', $textMonthNameSlug, ['class'=>'slug-publishmonthnumeric slug-segment']);
                $fullSlug = str_replace('{[publish_month_name]}', $c, $fullSlug);
                $cx[] = $textMonthNameSlug;
            }else if(strtolower($slugKeyword)=="{[slug]}"){
                $slugText = '';
                if($model->isNewRecord){
                    $slugText = 'slug-text';
                }else if(isset($slugSegment[$currentSegment]) && !empty($slugSegment[$currentSegment])){
                    $slugText = $slugSegment[$currentSegment];
                }else if(!empty($model->slug)){
                    $slugText = $model->slug;
                }else{
                    $slugText = \admin\helpers\String::slugify($model->title);
                }

                $c = Html::tag('span',
                            Html::tag('span', $slugText, ['class'=>'slug-click slug-segment', 'data'=>['input'=>'#slug-text']]) .
                            Html::textInput('slug', $slugText, ['class'=>'slug-input hidden', 'id'=>'slug-text']),
                            ['class'=>'slugtext']
                        );
                $fullSlug = str_replace('{[slug]}', $c, $fullSlug);
                $cx[] = $slugText;
            }else{
                $c = Html::tag('span', strtolower($slugKeyword), ['class'=>'slug-segment']);
                $fullSlug = preg_replace("/\{$slugKeyword}\//", "{$c}/", $fullSlug); //str_ireplace( strtolower($slugKeyword).'/', $c.'/', $fullSlug);
                // $fullSlug = str_replace('{[category]}', $c, $fullSlug);
            }

            // $text[] = $c;
            $currentSegment++;
        }
        // echo $text = implode('/', $text);
        echo Html::tag('p', $fullSlug, ['id'=>'full-slug-text']);
        if(empty($model->$attribute)){
            $model->$attribute = implode('/', $cx);
        }
        echo $form->field($model, 'slug', ['options'=>['class'=>['form-group field-slug has-success hidden']]])->textInput(['class'=>'full-input-slug'])->label(false);
        if($model->hasCustomAttribute('formattedSlug')){
            $model->formattedSlug = $fullSlug;
            echo $form->field($model, 'formattedSlug', ['options'=>['class'=>['form-group field-formated-slug has-success hidden']]])->textInput(['id'=>'formated-slug'])->label(false);
        }

        ?>

    </p>
</div>
