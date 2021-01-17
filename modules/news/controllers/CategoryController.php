<?php

namespace modules\news\controllers;

use modules\news\models\Category;
use modules\news\models\CategorySearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class'        => AccessControl::className(),
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        return $this->redirect('/site/login');
                    }
                    throw new HttpException(403, 'У вас нет доступа для выбранного действия');
                },
                'rules'        => [
                    [
                        'actions' => [],
                        'allow'   => true,
                        'roles'   => [
                            'news_category',
                        ],
                    ],
                ],
            ]
        ];
    }

    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();
        return $this->modify($model);
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        return $this->modify($model);
    }

    /**
     * @param $model Category
     * @return string|Response
     */
    public function modify($model)
    {
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            if (!empty($post['parent'])) {
                $parent = Category::findOne(['id' => $post['parent']]);
                if ($parent && $model->parent !== $parent && $parent !== $model) {
                    $model->appendTo($parent);
                }
            } else {
                $parent = Category::findOne(['depth' => 0]);
                if (!$parent) {
                    $model->makeRoot();
                } else {
                    if ($model->parent !== $parent && $parent !== $model) {
                        $model->appendTo($parent);
                    }
                }
            }
            $model->save();
            return $this->redirect(['index']);
        }
        $categories = Category::getList();
        if (!$model->isNewRecord) {
            unset($categories[$model->id]);
        }
        return $this->render('_form', [
            'model'      => $model,
            'categories' => $categories
        ]);
    }

    /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!$model->getDescendants()->all() && !$model->news) {
            $model->delete();
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при удалении. В категории есть новости или дочернии категории');
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
