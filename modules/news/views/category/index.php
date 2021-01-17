<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\news\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Категории (Тут только добавление и иерархия, привязка к новости на странице редактирования новостей)';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить категорию', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'options'      => ['class' => 'table-responsive'],
        'tableOptions' => ['class' => 'table table-condensed table-striped'],
        'rowOptions'   => function ($model, $key, $index, $grid) {
            return [
                'onclick' => 'window.location = "' . Url::to(['update', 'id' => $model->id]) . '"',
            ];
        },
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            [
                'attribute' => 'depth',
                'options'   => ['style' => 'width:50px'],
                'filter'    => false
            ],
            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{delete}',
            ],
        ],
        'layout'       => '{items}{pager}'
    ]); ?>
    <?php Pjax::end(); ?>
</div>
