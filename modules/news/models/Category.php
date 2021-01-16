<?php

namespace modules\news\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "portfolio_tag".
 *
 * @property int $id
 * @property string $name
 *
 * @property NewsCategory[] $newsCategories
 * @property News[] $news
 */
class Category extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
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
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getNewsCategories()
    {
        return $this->hasMany(NewsCategory::className(), ['category_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getNews()
    {
        return $this->hasMany(News::className(), ['id' => 'news_id'])->via('newsCategories');
    }
}
