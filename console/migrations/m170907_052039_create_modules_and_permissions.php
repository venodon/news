<?php

use yii\db\Migration;

/**
 * Class m181206_070750_create_modules_and_permissions
 */
class m170907_052039_create_modules_and_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('module', [
            'id'        => $this->primaryKey()->unsigned(),
            'name'      => $this->string(),
            'title'     => $this->string(),
            'parent_id' => $this->integer()->unsigned(),
            'icon'      => $this->string(20),
            'sort'      => $this->integer()->defaultValue(500)
        ]);

        $auth = Yii::$app->authManager;

        // создать разрешения
        $adminPanel = $auth->createPermission('adminPanel');
        $adminPanel->description = 'Доступ к админке';
        $auth->add($adminPanel);

        // создаем роли
        $user = $auth->createRole('user');
        $user->description = 'Пользователь';
        $auth->add($user);

        $manager = $auth->createRole('manager');
        $manager->description = 'Менеджер';
        $auth->add($manager);

        $admin = $auth->createRole('admin');
        $admin->description = 'Администратор';
        $auth->add($admin);

        // делаем наследование
        $auth->addChild($manager, $adminPanel);
        $auth->addChild($admin, $adminPanel);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('module');
    }
}
