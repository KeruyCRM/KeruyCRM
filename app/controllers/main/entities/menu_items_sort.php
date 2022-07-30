<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Menu_items_sort extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        //$menu_query = db_query("select * from app_entities_menu where id='" . db_input(\K::$fw->GET['id']) . "'");

        $menu = \K::model()->db_fetch_one('app_entities_menu', [
            'id = ?',
            \K::$fw->GET['id']
        ], [], 'entities_list');

        \K::$fw->entities_query = [];

        if ($menu) {
            /*$entities_query = db_query(
                "select * from app_entities e where e.id in (" . $menu['entities_list'] . ") order by field(e.id," . $menu['entities_list'] . ")"
            );*/

            \K::$fw->entities_query = \K::model()->db_fetch('app_entities', [
                'id in(' . $menu['entities_list'] . ')'
            ], ['order' => 'field(id,' . $menu['entities_list'] . ')'], 'id,name');
        }

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'menu_items_sort.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}