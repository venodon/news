<?php
/**
 * Created by PhpStorm.
 * User: suhov.a.s
 * Date: 26.07.2018
 * Time: 10:35
 */

namespace backend\widgets\adminImages;

use yii\base\Widget;

class AdminImages extends Widget
{
    public $model = 'model';

    public function run()
    {
        $content = $this->render('index', ['model' => $this->model]);
        return $content;
    }
}