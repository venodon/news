<?php

use yii\db\Migration;

/**
 * Class m181212_141915_create_config
 */
class m181207_075451_create_config extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('config', [
            'id'        => $this->primaryKey()->unsigned(),
            'parent_id' => $this->integer()->unsigned(),
            'slug'      => $this->string(50),
            'name'      => $this->string(200),
            'type'      => $this->smallInteger(2),
            'value'     => $this->text(),
            'variants'  => $this->text(),
            'sort'      => $this->integer()
        ]);
        $this->insert('module', ['name' => 'config', 'title' => 'Настройки', 'icon' => 'cogs']);

        $auth = Yii::$app->authManager;

        $config = $auth->createPermission('config');
        $config->description = 'Настройки';
        $auth->add($config);

//        $admin = $auth->getRole('admin');
//        $auth->addChild($admin, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('module', ['name' => 'config']);
        $this->dropTable('config');
    }
}
