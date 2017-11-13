<?php

namespace admin\models;

use Yii;

/**
 * This is the model class for table "options".
 *
 * @property string $id
 * @property string $key
 * @property string $value
 */
class Options extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'web_options';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value'], 'string'],
            [['key'], 'string', 'max' => 50],
            [['key'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'value' => 'Value',
        ];
    }

    protected function insertOptions($key, $value){
        $query = " REPLACE INTO `web_options` (`option_key`, `option_value`) VALUES (:key, :value); ";
        $command = Yii::$app->db->createCommand($query);
        $command->bindValue(":key", $key, \PDO::PARAM_STR);
        $command->bindValue(":value", $value, \PDO::PARAM_STR);
        return $command->execute();
    }
}
