<?php

namespace modules\users\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class RoleController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class'        => AccessControl::className(),
            'denyCallback' => function ($rule, $action) {
                return $this->redirect('/');
            },
            'rules'        => [
                [
                    'actions' => [],
                    'allow'   => true,
                    'roles'   => [
                        'users_role',
                    ],
                ],
            ],
        ];
        return $behaviors;
    }

    /**
     * Вывод списка ролей
     * @return string
     */
    public function actionIndex()
    {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRoles();

        return $this->render('index', [
            'roles' => $roles,
        ]);
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        $auth = Yii::$app->authManager;
        $code = Yii::$app->request->post('role')['code'];
        $role = $auth->createRole(!trim($code) ? 'role' . Yii::$app->security->generateRandomString(10) : $code);
        return $this->modify($role, true);
    }

    /**
     * @param $role
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($role)
    {
        $role = $this->findModel($role);
        return $this->modify($role);
    }

    /**
     * @param $role
     * @param bool $isNewModel
     * @return string
     * @throws \yii\base\Exception
     */
    public function modify($role, $isNewModel = false)
    {
        $auth = Yii::$app->authManager;
        $permissions = $auth->getPermissions();
        $rolePermissions = $auth->getPermissionsByRole($role->name);
        $errors = [];
        if (Yii::$app->request->post()) {
            $post = Yii::$app->request->post('permissions');
            $params = Yii::$app->request->post('role');

            if ($isNewModel) {
                if (!trim($params['description'])) {
                    $errors['description'][] = 'Role name can`t be blank';
                }
            }

            if (count($errors) === 0) {
                if ($isNewModel) {
                    if (!trim($params['code'])) {
                        $errors['code'][] = 'Code can`t be blank';
                    }

                    if (in_array($params['code'], ['admin', 'contentEditor', 'user', 'guest'])) {
                        $errors['code'][] = "Its impossible to create \"{$params['code']}\" role";
                    }
                }
            }

            if (count($errors) === 0) {
                $role->description = $params['description'];
                if ($isNewModel) {
                    $auth->add($role);
                } else {
                    $auth->update($role->name, $role);
                }

                foreach ($permissions as $key => $val) {
                    if (isset($post[$key]) && !isset($rolePermissions[$key])) {
                        $auth->addChild($role, $permissions[$key]);
                    }
                    if (!isset($post[$key]) && isset($rolePermissions[$key])) {
                        $auth->removeChild($role, $permissions[$key]);
                    }
                }

                $this->redirect(['index']);
            }
        }

        $models = [];
        $permissionsKeys = array_keys($permissions);
        foreach ($permissionsKeys as $permission) {
            $models[] = [
                'name'        => $permission,
                'description' => $permissions[$permission]->description,
                'assigned'    => array_key_exists($permission, $rolePermissions)
            ];
        }

        return $this->render('_form', [
            'models'     => $models,
            'role'       => $role,
            'isNewModel' => $isNewModel,
            'errors'     => $errors
        ]);
    }

    /**
     * @param $role
     * @return Response
     * @throws HttpException
     */
    public function actionDelete($role)
    {
        if (in_array($role, ['admin', 'user', 'guest'])) {
            throw new HttpException(403, 'Вы не можете удалить системные роли');
        }
        $auth = Yii::$app->authManager;
        $auth->remove($auth->getRole($role));

        return $this->redirect('index');
    }

    /**
     * @param $role
     * @return \yii\rbac\Role
     * @throws NotFoundHttpException
     */
    protected function findModel($role)
    {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRoles();

        if (isset($roles[$role])) {
            return $roles[$role];
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}