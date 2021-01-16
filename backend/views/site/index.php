<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Параметры';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(['id' => 'site-settings-form']); ?>
    <?= $form->field($model, 'siteName') ?>
    <?= $form->field($model, 'siteDescription') ?>
    <?= $form->field($model, 'editor') ?>
    <?= Html::submitButton('Сохранить') ?>
    <?php ActiveForm::end() ?>
</div>
