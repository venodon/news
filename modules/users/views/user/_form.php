<?php

use yii\helpers\Html;
use common\models\User;
use kartik\file\FileInput;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model common\models\User */
/* @var $rolesItems array */
/* @var $userRole */

$options = ImageHelper::getOptionsSingle($model, $model->image);

?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['method' => 'post', 'options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="row">
        <div class="col-xs-3">
            <?= FileInput::widget([
                'options'       => ['accept' => 'image/*'],
                'name'          => 'User[image]',
                'value'         => $model->image,
                'pluginOptions' => $options
            ]); ?>
        </div>
        <div class="col-xs-6">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>


            <div class="form-group">
                <?= Html::label('Пароль', 'password-field', ['class' => 'control-label']) ?>
                <?= Html::textInput('password', '', ['class' => 'form-control', 'id' => 'password-field', 'required' => $model->isNewRecord ? true : false]) ?>
            </div>

            <?= $form->field($model, 'status')->dropDownList(User::getStatuses()) ?>

            <?= $form->field($model, 'last_name') ?>

            <div class="form-group">
                <?= Html::label('Роль', 'userRole', ['class' => 'control-label']) ?>
                <?= Html::dropDownList('userRole', $userRole, $rolesItems, ['class' => 'form-control', 'id' => 'userRole']) ?>
            </div>

            <?= $form->field($model, 'position') ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
