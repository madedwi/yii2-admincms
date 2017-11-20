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
        foreach ($expl as $i=>$x) {
            if(strpos($x, '-') !== false){
                $_x = explode('-', $x);
                foreach ($_x as $_xi) {
                    $_expl[$_i] = $_xi;
                    $_i++;
                }
                $_i--;
            }else{
                $_expl[$_i] = $x;
            }
            $_i ++;
        }

        $expl = $_expl;
        $text = [];
        $cx = [];
        $slugSegment = [];
        $currentSegment = 0;

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
                $cx[] = $textCategorySlug;
            }else if(strtolower($slugKeyword)=="{[publish_year]}"){
                $textYearSlug = isset($slugSegment[$currentSegment]) && !empty($slugSegment[$currentSegment]) ? $slugSegment[$currentSegment] : date('Y');
                $c = Html::tag('span', $textYearSlug, ['class'=>'slug-publishyear slug-segment']);
                $cx[] = $textYearSlug;
            }else if(strtolower($slugKeyword)=="{[publish_month_numeric]}"){
                $textMonthSlug = isset($slugSegment[$currentSegment]) && !empty($slugSegment[$currentSegment]) ? $slugSegment[$currentSegment] : date('m');
                $c = Html::tag('span', $textMonthSlug, ['class'=>'slug-publishmonthnumeric slug-segment']);
                $cx[] = $textMonthSlug;
            }else if(strtolower($slugKeyword)=="{[publish_month_name]}"){
                $textMonthNameSlug = isset($slugSegment[$currentSegment]) && !empty($slugSegment[$currentSegment]) ? $slugSegment[$currentSegment] : date('F');
                $c = Html::tag('span', $textMonthNameSlug, ['class'=>'slug-publishmonthnumeric slug-segment']);
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
                $cx[] = $slugText;
            }else{
                $c = Html::tag('span', strtolower($slugKeyword), ['class'=>'slug-segment']);
            }

            $text[] = $c;
            $currentSegment++;
        }
        echo $text = implode('/', $text);
        if(empty($model->$attribute)){
            $model->$attribute = implode('/', $cx);
        }
        echo $form->field($model, 'slug')->hiddenInput(['class'=>'full-input-slug'])->label(false);
        ?>

    </p>
</div>
