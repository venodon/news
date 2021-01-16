<?php

namespace console\controllers;

use modules\config\models\Config;
use Yii;
use yii\console\Controller;

class SiteController extends Controller
{
    /**
     * Отправка логов ошибок техспециалисту
     */
    public function actionLogs()
    {
        $email = Yii::$app->params['techEmail'];
        if ($email) {
            $mail = Yii::$app->mailer->compose();
            $mail->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name]);
            $mail->setTo($email);
            $mail->setTextBody('Логи сайта "Reconcept"');
            $mail->setSubject('Логи сайта "Reconcept"');
            $dir = Yii::getAlias('@common/runtime/logs');
            $files = scandir($dir);
            $usedFiles = [];
            foreach ($files as $fileName) {
                if (in_array($fileName, ['.', '..', 'processed'])) {
                    continue;
                }
                $mail->attach(Yii::getAlias('@common/runtime/logs') . '/' . $fileName);
                $usedFiles[] = $fileName;
            }
            if ($usedFiles) {
                if ($mail->send()) {
                    $path = date('Ymd-His');
                    FileHelper::createDirectory($dir . '/processed/' . $path);
                    foreach ($usedFiles as $file) {
                        if (copy($dir . '/' . $file, $dir . '/processed/' . $path . '/' . $file)) {
                            unlink($dir . '/' . $file);
                        }
                    }
                }
            }
        }
    }

    public function actionFeedback()
    {
        $models = Support::find()->where(['status' => Support::STATUS_NEW])->all();
        foreach ($models as $model) {
            $mail = Yii::$app->mailer->compose('support', [
                'name' => $model->name, 'email' => $model->email, 'phone' => $model->phone, 'message' => $model->message
            ])->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                ->setTo(Config::getValue('requestEmail'))
                ->setSubject('Обращение')->send();
            if ($mail) {
                $model->status = Support::STATUS_WAIT;
                $model->save();
            }
        }
    }
}