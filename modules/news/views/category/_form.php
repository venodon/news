<?php

use kartik\select2\Select2;
use modules\news\models\Category;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model Category */
/* @var $form yii\widgets\ActiveForm */
/* @var $categories array */

$this->title = $model->isNewRecord ? 'Добавление категории' : 'Изменить категорию: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Категории', 'url' => ['index']];
if (!$model->isNewRecord) {
    $this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['_form', 'id' => $model->id]];
}
$this->params['breadcrumbs'][] = $model->isNewRecord ? 'Добавить' : 'Изменить';
?>
<h1><?= Html::encode($this->title) ?></h1>
<div class="category-update">
    <div class="category-form">

        <?php $form = ActiveForm::begin(); ?>
        <div class="row">

            <div class="col-xs-6">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>


                <div class="form-group">
                    <label class="control-label" for="cat-parent">Родительская категория</label>
                    <?= Select2::widget([
                        'name'    => 'parent',
                        'value'   => $model->parent ? $model->parent->id : '',
                        'data'    => $categories,
                        'options' => ['id' => 'cat-parent']
                    ]); ?>
                </div>

                <div class="form-group">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success button-save']) ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
