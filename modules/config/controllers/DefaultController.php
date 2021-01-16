<?php

namespace modules\config\controllers;

use modules\config\models\Config;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for Config model.
 */
class DefaultController extends Controller
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
                    throw new HttpException(403, 'You have not access here');
                },
                'rules'        => [
                    [
                        'actions' => ['create', 'index', 'update'],
                        'allow'   => true,
                        'roles'   => [
                            'config',
                        ],
                    ],
                    [
                        'actions' => ['create-part', 'delete-part'],
                        'allow'   => true,
                        'roles'   => [
                            'admin',
                        ],
                    ],
                ],
            ]
        ];
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionIndex()
    {
        $sub = Yii::$app->request->get('sub');
        if (!$sub) {
            $sub = 'site';
        }
        if (!Yii::$app->user->can('config_'.$sub)) {
            throw new HttpException(403, 'You have not access here');
        }
        if ($post = Yii::$app->request->post()) {
            $module = Config::findOne(['slug' => $sub]);
            if ($module) {
                $params = Config::find()->where(['parent_id' => $module->id])->all();
                foreach ($params as $param) {
                    /* @var $param Config */
                    if (!array_key_exists($param->slug, $post) && $param->type == Config::TYPE_CHECKBOX && $param->value == 1) {
                        $param->value = '';
                        if (!$param->save()) {
                            throw new Exception($param->getErrorSummary(true)[0]);
                        }
                    }
                }
            }
            foreach ($post as $k => $item) {
                if (mb_strpos($k, '_csrf')!==false) {
                    continue;
                }
                $model = Config::findOne(['slug' => $k]);
                if ($model) {
                    $model->value = $item;
                    if (!$model->save()) {
                        Yii::info(Json::encode($model->getErrorSummary(true)));
                    }
                }
            }
        }
        $parentConfig = Config::findOne(['slug' => $sub]);
        if (!$parentConfig) {
            throw new NotFoundHttpException();
        }
        $models = Config::find()->where(['parent_id' => $parentConfig->id])->orderBy(['sort' => SORT_ASC])->all();

        return $this->render('index', [
            'models'       => $models,
            'parentConfig' => $parentConfig
        ]);
    }

    /**
     * Creates a new Config model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Config();
        return $this->modify($model);
    }

    /**
     * Updates an existing Config model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param  integer  $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $model Config
     * @return string|\yii\web\Response
     */
    public function modify($model)
    {
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->cache->delete('config_parameter_'.$model->slug);
            return $this->redirect('index');
        }
        $categories = ArrayHelper::map(Config::find()->where([
            'or', ['parent_id' => 0], ['is', 'parent_id', null]
        ])->all(), 'id', 'name');
        return $this->render('_form', ['model' => $model, 'categories' => $categories]);
    }

    /**
     * Finds the Config model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param  integer  $id
     * @return Config the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Config::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionCreatePart()
    {
        $post = Yii::$app->request->post();
        if ($post && $post['name'] && $post['title']) {
            $name = $post['name'];
            $configModuleId = Yii::$app->db->createCommand("SELECT id FROM module WHERE name='config' AND parent_id IS NULL")->queryScalar();
            $test = Yii::$app->db->createCommand("SELECT id FROM module WHERE name='{$name}' AND parent_id='{$configModuleId}'")->queryScalar();
            if (!$test) {
                Yii::$app->db->createCommand()->insert('module', [
                    'name' => $post['name'], 'title' => $post['title'], 'parent_id' => $configModuleId,
                    'icon' => $post['icon']
                ])->execute();
                $auth = Yii::$app->authManager;
                $module = $auth->createPermission('config_'.$post['name']);
                $module->description = $post['title'];
                $auth->add($module);

                $admin = $auth->getRole('admin');
                $auth->addChild($admin, $module);

                Yii::$app->db->createCommand()->insert('config',
                    ['slug' => $post['name'], 'name' => $post['title']])->execute();
                return $this->redirect(['index', 'sub' => $post['name']]);
            }
        }
        return $this->render('part');
    }

    public function actionDeletePart($sub)
    {
        $id = (int) Yii::$app->db->createCommand("SELECT `id` FROM `config` WHERE slug='{$sub}' AND parent_id IS NULL")->queryScalar();
        Yii::$app->db->createCommand()->delete('config', ['parent_id' => $id])->execute();
        Yii::$app->db->createCommand()->delete('config', ['slug' => $sub, 'id' => $id])->execute();
        $auth = Yii::$app->authManager;
        $perm = $auth->getPermission('config_'.$sub);
        $auth->remove($perm);
        $configModuleId = Yii::$app->db->createCommand("SELECT id FROM module WHERE name='config' AND parent_id IS NULL")->queryScalar();
        Yii::$app->db->createCommand()->delete('module', ['name' => $sub, 'parent_id' => $configModuleId])->execute();
        return $this->redirect(['index']);
    }
}
