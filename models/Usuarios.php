<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "usuarios".
 *
 * @property int $id
 * @property string $nombre
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string|null $access_token
 * @property string|null $auth_key
 * @property int $admin
 * @property string|null $google_id
 * @property string|null $google_access_token
 * @property string $created_at
 * @property string $updated_at
 * @property string $modalClosed
 * @property string|null $dateModalClosed
 */
class Usuarios extends \yii\db\ActiveRecord implements IdentityInterface
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['access_token', 'auth_key', 'google_id', 'google_access_token'], 'default', 'value' => null],
            [['admin'], 'default', 'value' => 0],
            [['nombre', 'username'], 'required'],
            [['password_hash'], 'required', 'on' => 'create'],
            [['admin'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['nombre', 'password_hash', 'access_token', 'auth_key', 'google_id', 'google_access_token', 'email'], 'string', 'max' => 255],
            [['username'], 'string', 'max' => 100],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['email'], 'email'],
            [['username'], 'match', 'pattern' => '/^[a-zA-Z0-9_]+$/', 'message' => 'Username can only contain letters, numbers and underscores'],
            [['nombre'], 'string', 'min' => 2, 'max' => 255],
            [['modalClosed'], 'default', 'value' => '0'],
            [['dateModalClosed'], 'default', 'value' => null],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'nombre' => Yii::t('app', 'Nombre'),
            'username' => Yii::t('app', 'Username'),
            'email' => Yii::t('app', 'Email'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'access_token' => Yii::t('app', 'Access Token'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'admin' => Yii::t('app', 'Admin'),
            'google_id' => Yii::t('app', 'Google ID'),
            'google_access_token' => Yii::t('app', 'Google Access Token'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'modalClosed' => Yii::t('app', 'Modal Closed'),
            'dateModalClosed' => Yii::t('app', 'Date Modal Closed'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->generateAuthKey();
                $this->created_at = date('Y-m-d H:i:s');
                $this->modalClosed = '0';
                $this->dateModalClosed = null;
            }
            $this->updated_at = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
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
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Creates a new user with the given parameters
     *
     * @param string $username
     * @param string $password
     * @param string $nombre
     * @param int $admin
     * @return Usuarios|null
     */
    public static function createUser($username, $password, $nombre, $admin = 0)
    {
        $user = new static();
        $user->username = $username;
        $user->nombre = $nombre;
        $user->admin = $admin;
        $user->setPassword($password);
        
        if ($user->save()) {
            return $user;
        }
        
        return null;
    }

}
