<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * RegisterForm is the model behind the registration form.
 *
 */
class RegisterForm extends Model
{
    public $nombre;
    public $username;
    public $password;
    public $confirmPassword;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // All fields are required
            [['nombre', 'username', 'password', 'confirmPassword'], 'required'],
            
            // Username validation
            ['username', 'string', 'max' => 100],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9_]+$/', 'message' => 'Username can only contain letters, numbers and underscores'],
            ['username', 'unique', 'targetClass' => Usuarios::class, 'message' => 'This username is already taken'],
            
            // Nombre validation
            ['nombre', 'string', 'min' => 2, 'max' => 255],
            
            // Password validation
            ['password', 'string', 'min' => 6],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password', 'message' => 'Passwords do not match'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'nombre' => 'Nombre completo',
            'username' => 'Usuario',
            'password' => 'Contraseña',
            'confirmPassword' => 'Confirmar contraseña',
        ];
    }

    /**
     * Registers a new user with the provided data.
     * @return bool whether the user was registered successfully
     */
    public function register()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = Usuarios::createUser($this->username, $this->password, $this->nombre);
        
        if ($user) {
            // Automatically log in the user after successful registration
            return Yii::$app->user->login($user);
        }
        
        return false;
    }
}
