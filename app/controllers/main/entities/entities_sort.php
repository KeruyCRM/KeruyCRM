<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Entities_sort extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        \K::$fw->parent_id = \K::$fw->GET['parent_id'] ?? 0;

        if (\K::$fw->parent_id > 0) {
            /*\K::$fw->entities_query = db_query(
                "select id, name from app_entities where parent_id={$parent_id} order by sort_order, name"
            );*/

            \K::$fw->entities_query = \K::model()->db_fetch('app_entities', [
                'parent_id = ?',
                \K::$fw->parent_id
            ], ['order' => 'sort_order, name'], 'id,name');
        } else {
            /*$entities_query = db_query(
                "select id, name from app_entities where parent_id=0 and group_id=0 order by sort_order, name"
            );*/

            \K::$fw->entities_query = \K::model()->db_fetch('app_entities', [
                'parent_id = 0 and group_id = 0'
            ], ['order' => 'sort_order, name'], 'id,name');

            /*$groups_query = db_query(
                "select * from app_entities_groups " . ($entities_filter > 0 ? " where id={$entities_filter}" : "") . " order by sort_order, name"
            );*/

            \K::$fw->groups_query = \K::model()->db_fetch(
                'app_entities_groups',
                (\K::$fw->entities_filter > 0 ? ['id = ?', \K::$fw->entities_filter] : []),
                ['order' => 'sort_order, name'],
                'id,name'
            );
        }

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'entities_sort.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}