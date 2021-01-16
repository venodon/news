<?php

use yii\helpers\Html;
use modules\config\models\Config;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $categories array */
/* @var $model modules\config\models\Config */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="config-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'parent_id')->dropDownList($categories) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList(Config::TYPES) ?>

    <?= $form->field($model, 'variants')->label('Возможные варианты для выбора, через запятую (Только для выпадающих списков)') ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
