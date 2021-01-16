<?php
/**
 * Created by PhpStorm.
 * User: suhov.a.s
 * Date: 26.07.2018
 * Time: 10:37
 */

/* @var $model \yii\db\ActiveRecord */
?>

<div class="images-panel">
    <?php foreach ($model->images as $image) {
            echo $this->render('_image', ['model' => $model, 'image'=>$image]);
    } ?>
</div>
<div class="modal fade" id="setAlt" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="exampleModalLabel">Установить подпись</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="alt" class="form-control-label">Подпись:</label>
                        <input type="text" class="form-control" id="alt">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-admin js-set-img-alt" data-path="shop/item">Сохранить</button>
            </div>
        </div>
    </div>
</div>