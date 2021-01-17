<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel modules\news\models\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Новости';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="portfolio-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить новость', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'options'      => ['class' => 'table-responsive'],
        'tableOptions' => ['class' => 'table table-condensed table-striped'],
        'columns'      => [
            [
                'attribute' => 'id',
                'options'   => ['style' => 'width:50px'],
                'filter'    => false
            ],
            'name',
            [
                'attribute' => 'categories',
                'label'     => 'Категории',
                'value'     => static function ($model) {
                    $categories = ArrayHelper::getColumn($model->categories, 'name');
                    return implode(', ', $categories);
                },
                'filter'    => false,
            ],
            [
                'class'    => ActionColumn::class,
                'template' => '{update}',
            ],
            [
                'class'    => ActionColumn::class,
                'template' => '{delete}',
            ],
        ],
    ]); ?>
</div>
