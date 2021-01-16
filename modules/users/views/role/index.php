<?php
/**
 * Created by PhpStorm.
 * User: borod
 * Date: 08.08.2018
 * Time: 15:54
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $roles array */

$this->title = "Роли";
$this->params['breadcrumbs'][] = $this->title;
$i = 0;
?>
<div class="role-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <p>
        <?= Html::a('Добавить роль', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <table class="table table-striped table-bordered">
        <tr>
            <th>#</th>
            <th>Имя роли</th>
            <th>Код</th>
            <th></th>
            <th></th>
        </tr>
        <?php foreach ($roles as $code => $role): ?>
            <tr>
                <td style="width:40px">
                    <?= ++$i ?>
                </td>
                <td>
                    <?= $role->description ?>
                </td>
                <td><?= $code ?></td>
                <td style="width:30px">
                    <?= Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['update', 'role' => $code])) ?>
                </td>
                <td style="width: 30px">
                    <?php if (!in_array($code, ['admin', 'user', 'guest'])): ?>

                        <?= Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['delete', 'role' => $code]), [
                            "data-method"  => "post",
                            "data-confirm" => "Уверены что хотите удалить эту роль?"
                        ]) ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
    <?php Pjax::end() ?>
</div>