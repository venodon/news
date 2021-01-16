<?php

use modules\config\models\Config;
use yii\db\Migration;

/**
 * Class m181220_082415_add_site_config
 */
class m181220_082415_add_site_config extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $configModuleId = $this->db->createCommand("SELECT id FROM module WHERE name='config' AND parent_id IS NULL")->queryScalar();
        $this->insert('module', ['name' => 'site', 'title' => 'Настройки сайта', 'parent_id' => $configModuleId, 'icon' => 'address-card-o']);

        $auth = Yii::$app->authManager;
        $site = $auth->createPermission('config_site');
        $site->description = 'Настройки сайта';
        $auth->add($site);
        $admin = $auth->getRole('admin');
        $auth->addChild($admin, $site);

        $this->insert('config', ['slug' => 'site', 'name' => 'Настройки сайта']);
        $siteId = $this->db->createCommand("SELECT id FROM config WHERE slug='site'")->queryScalar();
        $params = [
            [$siteId, 'siteName', 'Название сайта', Config::TYPE_INPUT,'',500],
            [$siteId, 'siteDescription', 'Описание сайта', Config::TYPE_INPUT,'',499],
        ];
        $this->batchInsert('config', ['parent_id', 'slug', 'name', 'type', 'value', 'sort'], $params);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $site_id = $this->db->createCommand("SELECT id FROM config WHERE slug='site'")->queryScalar();
        $this->delete('config', ['parent_id' => $site_id]);
        $this->delete('config', ['and', ['slug' => 'site'], ['id' => $site_id]]);
        $configModuleId = $this->db->createCommand("SELECT id FROM module WHERE name='config' AND parent_id IS NULL")->queryScalar();
        $this->delete('module', ['parent_id' => $configModuleId, 'name' => 'site']);
        $auth = Yii::$app->authManager;
        $auth->remove($auth->getPermission('config_site'));
    }
}
