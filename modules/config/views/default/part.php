<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $categories array */
/* @var $model modules\config\models\Config */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="config-form">

    <?php $form = ActiveForm::begin(['method'=>'post'])?>

        <div class="form-group">
            <label class="control-label" for="name">Код</label>
            <input type="text" id="name" class="form-control" name="name">
        </div>
        <div class="form-group">
            <label class="control-label" for="title">Название</label>
            <input type="text" id="title" class="form-control" name="title">
        </div>
        <div class="form-group">
            <label class="control-label" for="icon">Иконка</label>
            <input type="text" id="icon" class="form-control" name="icon">
        </div>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>
    <?php ActiveForm::end()?>
</div>
