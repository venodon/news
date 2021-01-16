<?php

namespace modules\news\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "news".
 *
 * @property int $id
 * @property string $name
 * @property string $text
 *
 * @property Category[] $categories
 */
class News extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['text'], 'string', 'max' => 64000],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'   => 'ID',
            'name' => 'Название',
            'text' => 'Текст',
        ];
    }


    /**
     * @return ActiveQuery
     */
    public function getNewsCategories()
    {
        return $this->hasMany(NewsCategory::className(), ['news_id' => 'id']);
    }

    /**
     * @param $news
     */
    public function updateCategories($cats)
    {
        $oldCats = ArrayHelper::map($this->categories, 'name', 'id');
        $catsToInsert = array_diff($cats, $oldCats);
        $catsToDelete = array_diff($oldCats, $cats);
        NewsCategory::deleteAll(['and', ['news_id' => $this->id], ['category_id' => $catsToDelete]]);
        foreach ($catsToInsert as $ins) {
            if ((int)$ins == $ins) { // не переделывать на строгое сравнение ни при каких обстоятельствах
                $cat = Category::findOne(['id' => $ins]);
            } else {
                $cat = Category::findOne(['name' => mb_strtolower(Html::encode($ins))]);
            }
            if (!$cat) {
                $cat = new Category();
                $cat->name = mb_strtolower($ins);
                $cat->save();
            }
            $newsCat = new NewsCategory(['news_id' => $this->id, 'category_id' => $cat->id]);
            $newsCat->save();
        }
    }

    /**
     * @return ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['id' => 'category_id'])->via('newsCategories');
    }
}
