<?php
namespace admin\models;

use Yii;
use yii\db\Query;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use admin\db\WimaraAR;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends WimaraAR implements IdentityInterface
{
    const STATUS_DELETED = 'deleted';
    const STATUS_ACTIVE = 'active';
    const STATUS_SUSPEND = 'notactive';

    const SCENARIO_REGISTER = 'register';
    const SCENARIO_UPDATE = 'update';

    public $userType;
    public $password, $password_repeat, $password_old;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function scenarios(){
        $parentScenario = parent::scenarios();
        $parentScenario[self::SCENARIO_REGISTER] = ['username', 'firstname', 'lastname', 'email', 'phone', 'password', 'password_repeat', 'type'];
        $parentScenario[self::SCENARIO_UPDATE] = ['username', 'firstname', 'lastname', 'email', 'phone', 'password', 'password_repeat', 'type', 'password_old'];
        return $parentScenario;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'firstname'], 'required'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED, self::STATUS_SUSPEND]],
            [['username', 'email'], 'unique'],
            [['username', 'phone'], 'string', 'max'=>20],
            [['email', 'firstname', 'lastname'], 'string', 'max'=>100],
            [['timezone', 'type'], 'safe'],

            [['password', 'password_repeat', 'password_old', 'password_hash'], 'string'],
            [['password'], 'required', 'on'=>self::SCENARIO_REGISTER],
            ['password_repeat', 'compare', 'compareAttribute'=>'password'],
            ['password_old', 'validateOldPassword', 'on'=>self::SCENARIO_UPDATE]

        ];
    }

    public function validateOldPassword($attribute, $params, $validator){
        if(!$this->validatePassword($this->password_old)){
            $this->addError($attribute, 'Incorrect current password input.');
        }
    }

    public function beforeSave($insert){
        if($this->scenario == self::SCENARIO_REGISTER){
            $this->setPassword($this->password);
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function deleteUserType($typeKey){
        $hasUser = $this->query->select('id')->from(self::tableName())->where(['user.type'=>$typeKey])->count();
        if($hasUser>0){
            throw new \Exception("User(s) still used the selected type(s)");
        }else{
            $_ut = $this->userType;
            $userTypes = json_decode($_ut, true);
            foreach ($typeKey as $type) {
                unset($userTypes[$type]);
            }

            $query = $this->query->createCommand()->setSql("REPLACE INTO web_options (option_key, option_value) VALUES ('user_types', :option_value)");
            $query->bindParam(':option_value', $optionValue);
            $query->execute();
        }
    }

    public function getUserTypes(){
        $userType = $this->query->select('option_value')->from('web_options')->where(['option_key'=>'user_types'])->one();
        return json_decode($userType['option_value'], true);
    }

    public function saveUserType($typeKey, $typeValue){
        try {
            $userTypes = $this->userTypes;
            if(is_null($userTypes)){
                $userTypes = [$typeKey=>$typeValue];
            }else{
                $userTypes[$typeKey] = $typeValue;
            }

            $optionValue = json_encode($userTypes);

            $query = $this->query->createCommand()->setSql("REPLACE INTO web_options (option_key, option_value) VALUES ('user_types', :option_value)");
            $query->bindParam(':option_value', $optionValue);
            $query->execute();
            return true;
        } catch (\Exception $e) {
            $this->addError('userType', $e->getMessage());
            return false;
        }
    }

    public function removeUserType($typeKey){
        try {
            $userTypes = $this->userTypes;
            if(array_key_exists($typeKey, $userTypes)){
                unset($userTypes[$typeKey]);
                $this->query->createCommand()->update('web_options', [
                    'option_value' => json_encode($userTypes)
                ])->execute();
            }else{
                throw new \Exception("User type not found!");
            }
            return true;
        } catch (Exception $e) {
            $this->addError('userType', $e->getMessage());
            return false;
        }

    }

    public function updateStatus($status, $userID=NULL){
        if(is_null($userID)){
            $this->status = $status;
            return $this->save();
        }else{
            $update = $this->query->createCommand()->update(self::tableName(), [
                'status' => $status
            ],
            [
                'id' => $userID
            ]);

            return $update->execute();
        }

    }
}
