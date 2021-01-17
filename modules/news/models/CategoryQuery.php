<?php

namespace modules\news\models;

use paulzi\nestedsets\NestedSetsQueryTrait;
use yii\db\ActiveQuery;

class CategoryQuery extends ActiveQuery
{
    use NestedSetsQueryTrait;

    /**
     * @return CategoryQuery
     */
    public function root(): CategoryQuery
    {
        return $this->andWhere(['depth' => 1]);
    }
}