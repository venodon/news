<?php

namespace modules\users\controllers;

use common\models\user;
use modules\users\models\UserSearch;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * UserController implements the CRUD actions for user model.
 */
class UserController extends Controller
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
                            'users_user',
                        ],
                    ],
                ],
            ]
        ];
    }

    /**
     * Lists all user models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single user model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new user model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     * @throws Exception
     */
    public function actionCreate()
    {
        $model = new user();
        return $this->modify($model);
    }

    /**
     * Updates an existing user model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        return $this->modify($model);
    }

    /**
     * @param $model User
     * @return string|Response
     * @throws Exception
     */
    public function modify($model)
    {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRoles();
        $rolesItems = [];
        foreach ($roles as $role => $params) {
            $rolesItems[$role] = $params->description;
        }
        unset($roles['guest']);

        $userRoles = $auth->getRolesByUser($model->id);
        unset($userRoles['guest']);
        $userRolesKeys = array_keys($userRoles);
        $oldRole = $userRole = array_key_exists(0, $userRolesKeys) && $userRolesKeys[0] ? $userRolesKeys[0] : 'user';
        if (Yii::$app->request->isPost) {
            if (array_search(Yii::$app->request->post('userRole'), array_keys($roles)) !== false)
                $userRole = Yii::$app->request->post('userRole');

            if ($model->load(Yii::$app->request->post())) {
                $model->auth_key = Yii::$app->security->generateRandomString();
                if (Yii::$app->request->post('password')) {
                    $model->setPassword(Yii::$app->request->post('password'));
                }
                if ($model->save()) {
                    if ($oldRole !== $userRole) {
                        $auth->revokeAll($model->id);
                        $auth->assign($roles[$userRole], $model->id);
                        $model->role = $userRole;
                        $model->save();
                    }
                }
                return $this->redirect('index');
            }
        }
        return $this->render('_form', [
            'model'      => $model,
            'rolesItems' => $rolesItems,
            'userRole'   => $userRole
        ]);
    }

    /**
     * Deletes an existing user model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model) {
            $model->status = User::STATUS_DELETED;
            $model->save();
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the user model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return user the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = user::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
