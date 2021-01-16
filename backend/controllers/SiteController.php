<?php

namespace backend\controllers;

use common\models\LoginForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow'   => true,
                    ],
                    [
                        'actions' => ['upload', 'index', 'delete-file', 'cache', 'map'],
                        'allow'   => true,
                        'roles'   => ['adminPanel'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                    [
                        'actions' => ['auth'],
                        'allow'   => true,
                        'roles'   => [],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'cache'  => ['post'],
                    'map'    => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'auth'  => [
                'class'           => 'common\components\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }


    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(Yii::$app->params['front'].'/site/login?logout=1');
    }

    /**
     * Logout action.
     * @return string
     */
    public function actionCache()
    {
        Yii::$app->cache->flush();
        return $this->goHome();
    }

    /**
     * generate SiteMap
     * @return \yii\web\Response
     */
    public function actionMap()
    {
        SitemapHelper::sitemap();
        return $this->goHome();
    }

    /**
     * Удаление у моделей картинок, хранящихся в поле image
     * @return bool
     */
    public function actionDeleteFile()
    {
        $post = Yii::$app->request->post();
        $attribute = Yii::$app->request->get('field');
        if (!$attribute) {
            $attribute = 'image';
        }
        $model = $post['class']::findOne(['id' => $post['key']]);
        if ($model && $model->$attribute) {
            $file = Yii::getAlias('@webroot' . $model->$attribute);
            if (file_exists($file) && is_file($file)) {
                unlink($file);
            }
            $model->$attribute = '';
            if ($model->save()) {
                return true;
            } else {
                return $model->getErrorSummary(false)[0];
            }
        }
    }

    /**
     * @param $client
     */
    public function onAuthSuccess($client)
    {
        (new AuthHandler($client))->handle();
    }
}
