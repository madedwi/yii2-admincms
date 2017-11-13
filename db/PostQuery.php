<?php
namespace admin\db;

use yii\db\ActiveQuery;

class PostQuery extends ActiveQuery{

    public function __construct ( $modelClass, $post_type, $config = [] ){
        parent::__construct ( $modelClass, $config);


        if($post_type == 'page'){
            $this->andWhere(['post.type'=>'page']);
        }else if($post_type == 'post'){
            $this->andWhere(['post.type'=>'post']);
        }
    }

}
