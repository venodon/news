<?php
use modules\config\models\Config;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $parentConfig Config */
/* @var $models Config[] */

$this->title = $parentConfig->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['method' => 'post', 'options' => ['enctype' => 'multipart/form-data']]) ?>
    <?php foreach ($models as $model): ?>
        <?php if ($model->type === Config::TYPE_INPUT): ?>
            <div class="row">
                <div class="col-xs-7">
                    <?= $form->field($model, 'value')->textInput(['name' => $model->slug])->label($model->name) ?>
                </div>
            </div>
        <?php elseif ($model->type === Config::TYPE_INTEGER): ?>
            <div class="row">
                <div class="col-xs-7">
                    <?= $form->field($model, 'value')->textInput(['name' => $model->slug, 'type' => 'number'])->label($model->name) ?>
                </div>
            </div>
        <?php elseif ($model->type === Config::TYPE_NUMBER): ?>
            <div class="row">
                <div class="col-xs-7">
                    <?= $form->field($model, 'value')->textInput(['name' => $model->slug, 'type' => 'number', 'step' => '0.01'])->label($model->name) ?>
                </div>
            </div>
        <?php elseif ($model->type === Config::TYPE_CHECKBOX): ?>
            <div class="row">
                <div class="col-xs-7">
                    <div class="form-group">
                        <?= Html::Checkbox($model->slug, $model->value > 0, ['id' => '#checkbox'.$model->slug, 'class' => 'checkbox role-checkbox']) ?>
                        <?= Html::Label($model->name, '#checkbox'.$model->slug, ['class' => 'checkbox-label']); ?>
                    </div>
                </div>
            </div>
        <?php elseif ($model->type === Config::TYPE_SELECT): ?>
            <div class="row">
                <div class="col-xs-7">
                    <?= $form->field($model, 'value')->dropDownList($model->getVariants(), ['name' => $model->slug])->label($model->name) ?>
                </div>
            </div>
        <?php elseif ($model->type === Config::TYPE_PURE_TEXTAREA): ?>
            <div class="row">
                <div class="col-xs-7">
                    <?= $form->field($model, 'value')->textarea(['name' => $model->slug])->label($model->name) ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    <?php ActiveForm::end() ?>
</div>
