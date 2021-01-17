<?php

use kartik\select2\Select2;
use modules\news\models\News;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model News */
/* @var $form yii\widgets\ActiveForm */
/* @var $categories array */
?>

<div class="portfolio-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-xs-9">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'text')->textarea(['maxlength' => true]) ?>

            <?= $form->field($model, 'categories')->widget(Select2::classname(), [
                'data'          => $categories,
                'value'         => ArrayHelper::map($model->categories, 'name', 'name'),
                'language'      => 'ru',
                'options'       => ['placeholder' => 'Теги', 'multiple' => true],
                'pluginOptions' => [
                    'allowClear'         => true,
                    'tokenSeparators'    => [';'],
                    'maximumInputLength' => 255
                ],
            ]); ?>

        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success button-save']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <div class="buttons-panel">
        <?= Html::a('Отмена', Url::to('/news'), ['class' => 'btn btn-danger']) ?>
    </div>
</div>
