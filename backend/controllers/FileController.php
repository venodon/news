<?php

namespace backend\controllers;

use modules\config\models\Config;
use Yii;
use yii\base\DynamicModel;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Site controller
 */
class FileController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'delete-model-image', 'delete-image', 'delete-single-image', 'upload-image', 'sort-image', 'sort-file', 'delete-file',
                            'set-alt', 'process', 'import', 'editor-upload', 'upload', 'upload-gallery'
                        ],
                        'allow'   => true,
                        'roles'   => ['adminPanel'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete'       => ['POST'],
                    'delete-image' => ['POST'],
                    'sort-image'   => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function actions(): array
    {

        $maxWidth = Config::getValue('maxWidth');
        $maxHeight = Config::getValue('maxHeight');
        if (!$maxWidth) {
            $maxWidth = 2000;
        }
        if (!$maxHeight) {
            $maxHeight = 2000;
        }
        return [
            'editor-upload' => [
                'class'            => 'vova07\imperavi\actions\UploadFileAction',
                'url'              => '/uploads/images/ed',
                'path'             => '@frontend/web/uploads/images/ed',
                'translit'         => true,
                'validatorOptions' => [
                    'maxWidth'  => $maxWidth,
                    'maxHeight' => $maxHeight
                ],
            ],
            ''
        ];
    }


    /**
     * @return bool
     * @throws NotFoundHttpException
     */
    public function actionSetAlt(): bool
    {
        $class = Yii::$app->request->post('class');
        if (!$class) {
            $class = 'common\models\Image';
        }
        /* @var $model Image */
        $model = $class::findOne(['id' => Yii::$app->request->post('id')]);
        if ($model) {
            $model->alt = Yii::$app->request->post('value');
            $model->save();
            return true;
        }
        throw new NotFoundHttpException();
    }

    /**
     * Images sort
     * @param $id
     * @return bool
     * @throws MethodNotAllowedHttpException
     */
    public function actionSortImage($id): bool
    {
        $type = Yii::$app->request->get('type');
        if (!$type) {
            $type = 1;
        }
        if (Yii::$app->request->isAjax) {
            $sort = Yii::$app->request->post('sort');
            if ($sort['oldIndex'] > $sort['newIndex']) {
                $param = ['and', ['>=', 'sort', $sort['newIndex']], ['<', 'sort', $sort['oldIndex']], ['type' => $type]];
                $counter = 1;
            } else {
                $param = ['and', ['<=', 'sort', $sort['newIndex']], ['>', 'sort', $sort['oldIndex']], ['type' => $type]];
                $counter = -1;
            }
            Image::updateAllCounters(['sort' => $counter], [
                'and', ['class' => $sort['stack'][$sort['newIndex']]['class'], 'item_id' => $id], $param
            ]);
            Image::updateAll(['sort' => $sort['newIndex']], [
                'id' => $sort['stack'][$sort['newIndex']]['key']
            ]);
            return true;
        }
        throw new MethodNotAllowedHttpException();
    }

    /**
     * Files sort
     * @param $id
     * @return bool
     * @throws MethodNotAllowedHttpException
     */
    public function actionSortFile($id): bool
    {
        if (Yii::$app->request->isAjax) {
            $sort = Yii::$app->request->post('sort');
            if ($sort['oldIndex'] > $sort['newIndex']) {
                $param = ['and', ['>=', 'sort', $sort['newIndex']], ['<', 'sort', $sort['oldIndex']]];
                $counter = 1;
            } else {
                $param = ['and', ['<=', 'sort', $sort['newIndex']], ['>', 'sort', $sort['oldIndex']]];
                $counter = -1;
            }
            File::updateAllCounters(['sort' => $counter], [
                'and', ['class' => $sort['stack'][$sort['newIndex']]['class'], 'item_id' => $id], $param
            ]);
            File::updateAll(['sort' => $sort['newIndex']], [
                'id' => $sort['stack'][$sort['newIndex']]['key']
            ]);
            return true;
        }
        throw new MethodNotAllowedHttpException();
    }

    /**
     * @return bool
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDeleteImage(): bool
    {
        $post = Yii::$app->request->post();
        if (!empty($post['key'])) {
            $type = ArrayHelper::getValue($post, 'type');
            $model = Image::findOne(['id' => ArrayHelper::getValue($post, 'key'), 'type' => $type ?? Image::TYPE_IMAGE]);
            if ($model) {
                FileHelper::delete($model->image);
                FileHelper::delete($model->thumb);
                $model->delete();
                return true;
            }
        }
        throw new NotFoundHttpException();
    }

    public function actionDeleteSingleImage(): bool
    {
        $post = Yii::$app->request->post();
        $field = Yii::$app->request->get('field');
        if (!$field) {
            $field = 'image';
        }
        if (!empty($post['key']) && !empty($post['class'])) {
            $class = $post['class'];
            $model = $class::findOne(['id' => (int) $post['key']]);
            if ($model) {
                FileHelper::delete($model->$field);
                $model->$field = '';
                if ($model->save()) {
                    return true;
                }
            }
        }
        throw new NotFoundHttpException();
    }

    /**
     * @return bool
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteFile()
    {
        $post = Yii::$app->request->post();
        if (!empty($post['key'])) {
            $model = File::findOne(['id' => $post['key']]);
            if ($model) {
                $model->delete();
                return true;
            }
        }
        throw new NotFoundHttpException();
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function actionUploadImage()
    {
        if (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post('Image');
            if (!empty($post['item_id'])) {
                $model = new Image();
                $model->class = $post['class'];
                $model->item_id = $post['item_id'];
                if ($model->save()) {
                    $file = UploadedFile::getInstancesByName('images')[0];
                    $model->image = ImageHelper::uploadImage($model, $file, true);
                    if ($model->save()) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function actionUploadFile()
    {
        if (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post('Image');
            if (!empty($post['item_id'])) {
                $model = new File();
                $model->class = $post['class'];
                $model->item_id = $post['item_id'];
                if ($model->save()) {
                    $file = UploadedFile::getInstancesByName('images')[0];
                    $model->file = FileHelper::uploadFile($model, $file, true);
                    if ($model->save()) {
                        return true;
                    }
                }
            }
        }
        return false;
    }


    public function actionUpload()
    {
        $field = Yii::$app->request->post('field');
        $guid = Yii::$app->request->post('guid');
        $type = Yii::$app->request->post('type');
        $imageFile = UploadedFile::getInstanceByName($field);
        if (!$imageFile) {
            $imageFiles = UploadedFile::getInstancesByName($field);
            if ($imageFiles) {
                $imageFile = $imageFiles[0];
            }
        }
        if ($imageFile) {
            $dyn = new DynamicModel(compact('imageFile'));
            $dyn->addRule('imageFile', 'image')->validate();
            if ($dyn->hasErrors()) {
                Yii::$app->session->setFlash('warning', $dyn->getFirstError('imageFile'));
                return '';
            }
            $directory = Yii::getAlias('@frontend/web/uploads/temp').'/'.Yii::$app->session->id.'/'.$guid.'/'.$type.'/';
            if (!is_dir($directory)) {
                FileHelper::createDirectory($directory);
            }
            $fileName = $imageFile->name;
            $filePath = $directory.$fileName;
            if ($imageFile->saveAs($filePath)) {
                $path = Yii::getAlias('@frontend/web/uploads/temp').'/'.Yii::$app->session->id.'/'.$guid.'/'.$type.'/'.$fileName;
                ImageHelper::watermark($path);
                ImageHelper::crop($path, true, null,
                    Config::getValue('cropWidth'),
                    Config::getValue('cropHeight'), true, true);
                return Json::encode([
                    'files' => [
                        [
                            'name' => $fileName,
                            'path' => $path,
                        ],
                    ],
                ]);
            }
        } else {
            return '{}';
        }
        return '';
    }

    public function actionUploadGallery()
    {
        $field = Yii::$app->request->post('field');
        $guid = Yii::$app->request->post('guid');
        $imageFile = UploadedFile::getInstanceByName($field);
        if (!$imageFile) {
            $imageFiles = UploadedFile::getInstancesByName($field);
            if ($imageFiles) {
                $imageFile = $imageFiles[0];
            }
        }
        if ($imageFile) {
            $dyn = new DynamicModel(compact('imageFile'));
            $dyn->addRule('imageFile', 'image')->validate();
            if ($dyn->hasErrors()) {
                Yii::$app->session->setFlash('warning', $dyn->getFirstError('imageFile'));
                return '';
            }
            $directory = Yii::getAlias('@frontend').'/web/uploads/temp/'.Yii::$app->session->id.'/'.$guid.'/';
            if (!is_dir($directory)) {
                FileHelper::createDirectory($directory);
            }
            $fileName = $imageFile->name;
            $filePath = $directory.$fileName;
            if ($imageFile->saveAs($filePath)) {
                $path = $directory.$fileName;
                ImageHelper::watermark($path);
                ImageHelper::crop($path, true, null,
                    Config::getValue('cropWidth'),
                    Config::getValue('cropHeight'), true, true);
                return Json::encode([
                    'files' => [
                        [
                            'name' => $fileName,
                            'path' => $path,
                        ],
                    ],
                ]);
            }
        } else {
            return '{}';
        }
        return '';
    }
}
