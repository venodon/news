<?php

namespace modules\news\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "news_category".
 *
 * @property string $id
 * @property int $news_id
 * @property int $category_id
 *
 * @property News $news
 * @property Category $category
 */
class NewsCategory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['news_id', 'category_id'], 'integer'],
            [['news_id', 'category_id'], 'unique', 'targetAttribute' => ['news_id', 'category_id']],
            [['news_id'], 'exist', 'skipOnError' => true, 'targetClass' => News::className(), 'targetAttribute' => ['news_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'news_id'     => 'Новость',
            'category_id' => 'Категория',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getNews()
    {
        return $this->hasOne(News::className(), ['id' => 'news_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
}
