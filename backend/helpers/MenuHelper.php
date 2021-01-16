<?php
/**
 * Created by PhpStorm.
 * User: adm
 * Date: 06.12.2018
 * Time: 23:11
 */

namespace backend\helpers;


use yii\db\Query;

class MenuHelper
{
    /**
     * @param $addMain
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getMenu($addMain = true)
    {
        $result = [];
        if ($addMain) {
            $result[] = ['label' => 'Админка', 'options' => ['class' => 'header']];
        }
        $modules = self::buildMenu();
        foreach ($modules as $module) {
            if (\Yii::$app->user->can($module['name'])) {
                $result[$module['name']] = [
                    'label' => $module['title'],
                    'icon' => $module['icon'],
//                    'options' => ['class' => 'menu-open']
                ];
                if (array_key_exists('items', $module) && is_array($module['items'])) {
                    foreach ($module['items'] as $item) {
                        if (\Yii::$app->user->can(str_replace('/', '_', $item['name']))) {
                            $is_config = mb_strpos($item['name'], 'config') === 0;
                            if ($is_config) {
                                $subModule = str_replace('config/', '', $item['name']);
                            }
                            $result[$module['name']]['items'][] = [
                                'label' => $item['title'],
                                'icon' => $item['icon'],
                                'url' => $is_config && !empty($subModule) ? '/config?sub=' . $subModule : '/' . $item['name']
                            ];
                        }

                    }
                } else {
                    $result[$module['name']]['url'] = '/'.$module['name'];
                }
            }
        }
        return $result;
    }

    /**
     * Делаем структуру меню в админке
     * @return array
     * @throws \yii\db\Exception
     */
    private static function buildMenu()
    {
        $query = new Query();
        $modules = $query->select('*')
            ->from('module')
            ->orderBy(['sort' => SORT_ASC])
            ->createCommand()
            ->queryAll();

        foreach ($modules as $k => $module) {
            $modules[$k]['id'] = (int)$module['id'];
            $module[$k]['parent_id'] = (int)$module['parent_id'];
        }

        $result = [];

        foreach ($modules as $model) {
            if ($model['parent_id'] > 0)
                $result[$model['parent_id']]['items'][] = ['name' => $result[$model['parent_id']]['name'] . '/' . $model['name'], 'title' => $model['title'], 'icon' => $model['icon']];
            else {
                $result[$model['id']]['name'] = $model['name'];
                $result[$model['id']]['title'] = $model['title'];
                $result[$model['id']]['icon'] = $model['icon'];
            }
        }

        return $result;
    }
}