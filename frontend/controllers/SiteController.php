<?php

namespace frontend\controllers;

use modules\news\models\Category;
use modules\news\models\News;
use Yii;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{

    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $models = Category::find()->select(['id', 'name', 'depth'])->where(['>', 'depth', 0])->all();
        return $models;
    }

    public function actionCategory()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = (int)Yii::$app->request->get('id');
        $depth = (int)Yii::$app->request->get('depth');
        if ($id === 1) {
            Yii::$app->response->setStatusCode(403);
            return ['status' => 'error', 'message' => 'Категория закрыта для получения'];
        }
        $category = Category::findOne(['id' => $id]);
        if ($category) {
            $children = $category->getDescendants($depth, true)->all();
            $catsIds = ArrayHelper::getColumn($children, 'id');
            $models = News::find()->joinWith('newsCategories')->where(['in', 'category_id', $catsIds])->all();
            return ['status' => 'success', 'news' => $models];
        }
        return ['status' => 'fail', 'message' => 'Ошибка при получении данных'];
    }
}
