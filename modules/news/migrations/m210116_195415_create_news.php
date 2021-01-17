<?php

use modules\news\models\Category;
use yii\db\Migration;

/**
 * Class m190129_071015_create_news
 */
class m210116_195415_create_news extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // таблицы
        $this->createTable('news', [
            'id'   => $this->primaryKey()->unsigned(),
            'name' => $this->string(),
            'text' => $this->text(),
        ]);

        $this->createTable('category', [
            'id'    => $this->primaryKey()->unsigned(),
            'name'  => $this->string(),
            'lft'   => $this->integer(),
            'rgt'   => $this->integer(),
            'depth' => $this->integer(),
        ]);
        $this->createIndex('lft', 'category', ['lft', 'rgt']);

        $this->createTable('news_category', [
            'id'          => $this->bigPrimaryKey()->unsigned(),
            'news_id'     => $this->integer()->unsigned(),
            'category_id' => $this->integer()->unsigned(),
        ]);
        $this->addForeignKey('fk_news_category_category', 'news_category', 'category_id', 'category', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk_news_category_news', 'news_category', 'news_id', 'news', 'id', 'cascade', 'cascade');

        // модули
        $this->insert('module', ['name' => 'news', 'title' => 'Новости', 'icon' => 'book']);
        $id = $this->db->createCommand("SELECT id FROM module WHERE name='news' AND parent_id IS NULL")->queryScalar();
        $newsModules = [
            ['category', 'Категории', $id, 'list'],
            ['news', 'Новости', $id, 'file-text'],
        ];
        $this->batchInsert('module', ['name', 'title', 'parent_id', 'icon'], $newsModules);


        // разрешения
        $auth = Yii::$app->authManager;

        $news = $auth->createPermission('news');
        $news->description = 'Модуль новостей';
        $auth->add($news);

        $categories = $auth->createPermission('news_category');
        $categories->description = 'Категории';
        $auth->add($categories);

        $posts = $auth->createPermission('news_news');
        $posts->description = 'Новости';
        $auth->add($posts);


        $admin = $auth->getRole('admin');
        $auth->addChild($admin, $news);
        $auth->addChild($admin, $categories);
        $auth->addChild($admin, $posts);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_news_category_category', 'news_category');
        $this->dropForeignKey('fk_news_category_news', 'news_category');

        $this->dropTable('news_category');
        $this->dropTable('news');
        $this->dropTable('category');

        $id = $this->db->createCommand("SELECT id FROM module WHERE name='news' AND parent_id IS NULL")->queryScalar();
        $this->delete('module', ['parent_id' => $id]);
        $this->delete('module', ['and', ['name' => 'news'], ['id' => $id]]);

        $auth = Yii::$app->authManager;
        $auth->remove($auth->getPermission('news_news'));
        $auth->remove($auth->getPermission('news_category'));
        $auth->remove($auth->getPermission('news'));
    }
}
