<?php
namespace common\components;


use Yii;
use admin\models\AcitivityLog;
use yii\base\Component;
use yii\base\InvalidConfigException;

class ActivityLog extends Component{


    public function insertLog($table, $activity){
        $activityDate   = Yii::$app->dateTime->serverTime()->format('Y-m-d H:i:s');
        $userId         = Yii::$app->user->identity->id;
        $result = true;
        try {
            $model = new AcitivityLog();
            $model->table = $table;
            $model->activity = $activity;
            $model->activitydate = $activityDate;
            $model->user_id = $userId;
            if(!$model->save()){
                throw new Exception("Cannot save activity log.", 1);
            }
        } catch (Exception $e) {
            $result = $e->getMessage();
        }finally{
            return $result;
        }

    }
}
