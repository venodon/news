<?php

namespace modules\news\models;

use paulzi\nestedsets\NestedSetsBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "news_category".
 *
 * @property int $id
 * @property int $lft
 * @property int $rgt
 * @property int $depth
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
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class'         => NestedSetsBehavior::class,
                'treeAttribute' => null
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lft', 'rgt', 'depth'], 'integer'],
            [['name'], 'string'],
            [['name'], 'unique'],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new CategoryQuery(static::class);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'    => 'ID',
            'name'  => 'Название',
            'lft'   => 'Lft',
            'rgt'   => 'Rgt',
            'depth' => 'Depth',
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

    /**
     * @return array
     */
    public static function getList()
    {
        return ArrayHelper::map(self::find()->where(['>', 'depth', 0])->all(), 'id', 'name');
    }
}
