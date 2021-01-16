<?php
/**
 * Created by PhpStorm.
 * User: adm
 * Date: 11.12.2018
 * Time: 16:47
 */
/* @var $image \common\models\Image */
/* @var $model \yii\db\ActiveRecord */

use yii\helpers\Html; ?>

<div class="image-admin-preview" data-id="<?= !empty($image->id) ? $image->id : '' ?>" data-file="<?= $image->image ?>">
    <?php if ($image->id && $image->item_id): ?>
        <?php if ($image->is_main): ?>
            <div class="default-image">
                <span class="glyphicon glyphicon-ok" title="Основное изображение"></span>
            </div>
        <?php else: ?>
            <div class="js-set-default-image">
                <span class="glyphicon glyphicon-ok" title="Сделать основным"></span>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <div class="js-image-admin-delete" data-path="shop/item">
        <span class="glyphicon glyphicon-trash" title="Удалить изображение"></span>
    </div>
    <div class="img-alt <?= $image->alt ? 'green' : '' ?>" data-toggle="modal" data-id="<?= $image->id ?>"
         data-path="shop/item" data-target="#setAlt"
         title="<?= $image->alt ?? 'Добавить подпись' ?>">
        <span class="glyphicon glyphicon-pencil"></span>
    </div>
    <?= Html::img($image->image, ['class' => 'img-admin']) ?>
</div>

