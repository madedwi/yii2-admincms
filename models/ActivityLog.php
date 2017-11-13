<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "activity_log".
 *
 * @property integer $id
 * @property string $activitydate
 * @property string $table
 * @property string $activity
 * @property integer $user_id
 */
class ActivityLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activity_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activitydate'], 'safe'],
            [['table'], 'required'],
            [['activity'], 'string'],
            [['user_id'], 'integer'],
            [['table'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'activitydate' => 'Activitydate',
            'table' => 'Table',
            'activity' => 'Activity',
            'user_id' => 'User ID',
        ];
    }
}
