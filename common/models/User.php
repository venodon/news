<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $role
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $last_name
 * @property string $first_name
 * @property string $name
 * @property string $phone
 * @property string $address
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 *
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const STATUS_DELETED = 20;
    public const STATUS_DISABLED = 0;
    public const STATUS_ACTIVE = 10;

    public const GENDER_MAN = 0;
    public const GENDER_WOMAN = 1;
    public const GENDER_LIST = [
        self::GENDER_MAN   => 'Муж',
        self::GENDER_WOMAN => 'Жен'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    /**
     * @param  bool  $insert
     * @return bool
     */
    public function beforeSave($insert): bool
    {
        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['email', 'address'], 'string', 'max' => 255],
            [['last_name', 'first_name', 'phone'], 'string', 'max' => 85],
            [['role'], 'string', 'max' => 30],
            [['email', 'status'], 'required'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id'                   => 'ID',
            'email'                => 'Email',
            'password_hash'        => 'Password hash',
            'password_reset_token' => 'Password reset token',
            'role'                 => 'Роль',
            'status'               => 'Статус',
            'address'              => 'Адрес',
            'last_name'            => 'Имя',
            'first_name'           => 'Фамилия',
            'phone'                => 'Телефон',
            'created_at'           => 'Created at',
            'updated_at'           => 'Updated at'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by email
     *
     * @param  string  $email
     * @return static|null
     */
    public static function findByUsername($email): ?User
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param  string  $token  password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token): ?User
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status'               => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param  string  $token  password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token): bool
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password  password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @param $password
     * @throws Exception
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
        $this->password_reset_token = Yii::$app->security->generateRandomString().'_'.time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }


    public static function getStatuses()
    {
        return [
            self::STATUS_DISABLED => 'Отключен',
            self::STATUS_ACTIVE   => 'Активен'
        ];
    }

    /**
     * @param $user
     * @param  null  $password
     */
    public static function sendRegLetter($user, $password = null)
    {
        Yii::$app->mailer->compose('registration', ['model' => $user, 'password' => $password])
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($user->email)
            ->setSubject('Вы зарегистрированы на сайте '.Yii::$app->name)
            ->send();
    }

    /**
     * Получаем список всех ролей (кроме гостя)
     * @return array
     */
    public static function getAccessTypes()
    {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRoles();
        $result = [];
        foreach ($roles as $name => $role) {
            if ($name === 'guest') {
                continue;
            }
            $result[$name] = $role->description;
        }

        return $result;
    }

    /**
     * @return array
     */
    public static function getAuthors()
    {
        return User::find()->alias('u')->select(['u.email', 'u.id'])
            ->where(['in', 'u.role', ['admin', 'manager']])->indexBy('id')->column();
    }

    /**
     * @return User|null|IdentityInterface
     */
    public static function getUser()
    {
        return Yii::$app->user->identity;
    }

    /**
     * @return array
     */
    public static function getUserRoleList()
    {
        $roleList = User::find()->select('role')->distinct()->all();
        return ArrayHelper::map($roleList, 'role', 'role');
    }



    /**
     * @return string
     */
    public function getAvatar()
    {
        return Yii::$app->params['defaultAvatar'];
    }


    public function getName()
    {
        return $this->last_name.' '.$this->first_name;
    }
}
