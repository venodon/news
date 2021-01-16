<?php

use common\models\User;
use yii\db\Migration;

/**
 * Class m181212_142805_users_module
 */
class m181212_142805_users_module extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $userPermission = $auth->createPermission('users');
        $userPermission->description = 'Доступ к админке пользователей';
        $auth->add($userPermission);

        $users = $auth->createPermission('users_user');
        $users->description = 'Управление пользователями';
        $auth->add($users);

        $roles = $auth->createPermission('users_role');
        $roles->description = 'Управление ролями';
        $auth->add($roles);

        $admin = $auth->getRole('admin');

        $auth->addChild($admin, $userPermission);
        $auth->addChild($admin, $users);
        $auth->addChild($admin, $roles);

        // создаем админского пользователя
        $userAdmin = new User([
            'email'    => 'venodon@gmail.com',
            'status'   => User::STATUS_ACTIVE,
            'role'     => 'admin'
        ]);
        $userAdmin->setPassword('123321');
        $userAdmin->generateAuthKey();
        if (!$userAdmin->save()) {
            var_dump($userAdmin->errors);
            die;
        }

        $this->insert('module', ['name' => 'users', 'title' => 'Пользователи и роли', 'icon' => 'user']);

        $id = $this->db->createCommand("SELECT id FROM module WHERE name='users' AND parent_id IS NULL")->queryScalar();
        $usersModules = [
            ['user', 'Пользователи', $id, 'users'],
            ['role', 'Роли', $id, 'user-plus'],
        ];
        $this->batchInsert('module', ['name', 'title', 'parent_id', 'icon'], $usersModules);
        $auth->assign($admin, $userAdmin->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $id = $this->db->createCommand("SELECT id FROM module WHERE name='users' AND parent_id IS NULL")->queryScalar();
        $this->delete('module', ['parent_id' => $id]);
        $admin = $this->db->createCommand("SELECT id FROM user WHERE email='admin'")->queryScalar();
        if ($admin) {
            $this->delete('user_auth', ['user_id' => $admin]);
            $this->delete('user', ['email' => 'admin']);
        }
        $this->delete('module', ['and', ['name' => 'users'], ['id' => $id]]);
        $auth = Yii::$app->authManager;
        $auth->remove($auth->getPermission('users_role'));
        $auth->remove($auth->getPermission('users_user'));
        $auth->remove($auth->getPermission('users'));
        $auth->remove($auth->getRole('admin'));
    }
}
