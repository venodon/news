<?php

use yii\helpers\Html;
use common\models\User;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\users\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить пользователя', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'options'      => ['class' => 'table-responsive'],
        'tableOptions' => ['class' => 'table table-condensed table-striped'],
        'rowOptions'   => function ($model, $key, $index, $grid) {
            return [
                'onclick' => 'window.location = "'.Url::to(['update', 'id' => $model->id]).'"',
            ];
        },
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'headerOptions' => ['width' => 120],
                'options'       => ['style' => 'width:120px'],
                'attribute'     => 'image',
                'format'        => 'html',
                'value'         => function ($model) {
                    return Html::img($model->image, ['class' => 'admin-grid-image']);
                },
                'filter'        => false
            ],
            'email:email',
            [
                'attribute' => 'status',
                'format'    => 'html',
                'filter'    => Html::dropDownList('UserSearch[status]', $searchModel->status, [10 => 'Активен', 0 => 'Отключен'],
                    ['prompt' => '', 'class' => 'form-control']),
                'value'     => function ($model) {
                    return $model->status === User::STATUS_ACTIVE ? '<span style="color:green;">активен</span>' : '<span style="color:red;">отключен</span>';
                }
            ],
            [
                'attribute' => 'role',
                'filter'    => Html::dropDownList('UserSearch[role]', $searchModel->role, User::getUserRoleList(), ['prompt' => '', 'class' => 'form-control']),
                'value'     => function ($model) {
                    return $model->role;
                }
            ],

            ['class' => 'yii\grid\ActionColumn', 'template' => '{delete}',],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
