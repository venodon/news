<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $categories array */
/* @var $model modules\news\models\News */

$this->title = $model->isNewRecord ? 'Добавить новость' : 'Изменить новость: ' . $model->name;
?>
<div class="portfolio-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model'      => $model,
        'categories' => $categories,
    ]) ?>

</div>
