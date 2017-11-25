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
class Options extends \admin\db\WimaraAR
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'web_options';
    }

    public function init(){
        parent::init();

        $attributes = $this->getCustomAttributes();
        $attrkey    = array_keys($attributes);

        $data = self::find()->where(['option_key' => $attrkey])->asArray()->all();
        foreach ($data as $option) {
            $this->setCustomAttribute($option['option_key'], $option['option_value']);
        }
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
