<?php
/**
 * Created by PhpStorm.
 * User: venod
 * Date: 15.03.2019
 * Time: 14:26
 */

namespace common\models;


use yii\helpers\Inflector;
use yii\helpers\Json;

class MActiveRecord extends \yii\db\ActiveRecord
{
    public function getUniqueSlug($slug = '')
    {
        if ($slug) {
            if ($this->isNewRecord) {
                $exists = static::findOne(['slug' => $slug]);
            } else {
                $exists = static::find()->where(['slug' => $slug])->andWhere(['!=', 'id', $this->id])->one();
            }
            if ($exists) {
                $models = static::find()->where(['like', 'slug', $slug.'_'])->all();
                if (!$models) {
                    $slug .= '_1';
                } else {
                    $max = 0;
                    foreach ($models as $model) {
                        $arr = explode('_', $model->slug);
                        $num = (int) end($arr);
                        if ($num > $max) {
                            $max = $num;
                        }
                    }
                    $slug .= '_'.++$max;
                }
            }
        }
        $this->slug = $slug;
    }

    /**
     * @param  bool  $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->hasAttribute('slug')) {
            if ($this->slug) {
                $slug = $this->slug;
            } else {
                $source = $this->generateSlug();
                $slug = Inflector::slug($source);
            }
            $this->getUniqueSlug($slug);
        }
        if ($this->hasAttribute('created_at')) {
            $time = date('Y-m-d H:i:s');
            if ($this->isNewRecord && !$this->created_at) {
                $this->created_at = $time;
            }
            $this->updated_at = $time;
            if ($this->errors) {
                \Yii::info($this->getErrorSummary(false)[0]);
            }
        }
        return parent::beforeSave($insert);
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        $result = parent::save($runValidation, $attributeNames);
        if ($this->hasErrors()) {
            \Yii::info(Json::encode($this->getErrorSummary(true)));
        }
        return $result;
    }

    public function generateSlug()
    {
        return $this->name;
    }

}