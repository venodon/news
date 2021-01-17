<?php

namespace modules\news\controllers;

use modules\news\models\Category;
use modules\news\models\News;
use modules\news\models\NewsSearch;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * DefaultController implements the CRUD actions for News model.
 */
class NewsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        return $this->redirect('/site/login');
                    } else {
                        throw new HttpException(403, 'У вас нет доступа для выбранного действия');
                    }
                },
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => [
                            'adminPanel',
                        ],
                    ],
                    [
                        'actions' => ['create', 'update', 'delete', 'view', 'image-upload', 'main'],
                        'allow' => true,
                        'roles' => [
                            'news_news',
                        ],
                    ],
                ],
            ]
        ];
    }

    /**
     * Lists all News models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];
        $dataProvider->pagination->pageSize = 100;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return array|string|Response
     * @throws Exception
     */
    public function actionCreate()
    {
        $model = new News();
        return $this->modify($model);
    }

    /**
     * @param $id
     * @return array|string|Response
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (!$model) {
            throw new NotFoundHttpException();
        }
        return $this->modify($model);
    }

    /**
     * @param $model News
     * @return array|string|Response
     * @throws Exception
     */
    public function modify($model)
    {
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        $post = Yii::$app->request->post();

        if ($model->load($post)) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Новость успешно отредактирована');
                $categories = $post['News'] && $post['News']['categories'] ? $post['News']['categories'] : [];
                $model->updateCategories($categories);
                return $this->redirect(['index']);
            }
        }
        $categories = ArrayHelper::map(Category::find()->where(['>', 'depth', 0])->all(), 'id', 'name');
        return $this->render('update', [
            'model' => $model,
            'categories' => $categories,
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Portfolio model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
