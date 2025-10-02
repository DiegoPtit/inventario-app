<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\RegisterForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\authclient\widgets\AuthChoice;

$this->title = 'Registrarse';
?>
<div class="site-register">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Por favor, complete los siguientes campos para crear su cuenta:</p>

    <div class="row">
        <div class="col-lg-5">

            <?php $form = ActiveForm::begin([
                'id' => 'register-form',
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => 'col-lg-1 col-form-label mr-lg-3'],
                    'inputOptions' => ['class' => 'col-lg-3 form-control'],
                    'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
                ],
            ]); ?>

            <?= $form->field($model, 'nombre')->textInput(['autofocus' => true])->label('Nombre completo') ?>

            <?= $form->field($model, 'username')->textInput()->label('Usuario') ?>

            <?= $form->field($model, 'password')->passwordInput()->label('Contraseña') ?>

            <?= $form->field($model, 'confirmPassword')->passwordInput()->label('Confirmar contraseña') ?>

            <div class="form-group">
                <div>
                    <?= Html::submitButton('Registrarse', ['class' => 'btn btn-success w-100', 'name' => 'register-button']) ?>
                </div>
            </div>

            <div class="form-group mt-3">
                <div>
                    <?= Html::a('Ya tengo cuenta - Iniciar sesión', ['site/login'], ['class' => 'btn btn-outline-primary w-100']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

            <!-- Separador -->
            <div class="text-center my-4">
                <hr>
                <span class="text-muted">O regístrate con</span>
                <hr>
            </div>

            <!-- Botón de Google -->
            <div class="form-group">
                <div>
                    <?= Html::a(
                        '<svg width="18" height="18" viewBox="0 0 24 24" class="me-2">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Registrarse con Google',
                        ['site/auth', 'authclient' => 'google'],
                        [
                            'class' => 'btn btn-outline-primary w-100 d-flex align-items-center justify-content-center',
                            'style' => 'height: 45px; text-decoration: none;'
                        ]
                    ) ?>
                </div>
            </div>

        </div>
    </div>
</div>
