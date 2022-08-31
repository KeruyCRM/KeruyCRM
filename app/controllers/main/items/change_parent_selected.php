<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Change_parent_selected extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'change_parent_selected.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function change_parent()
    {
        $parent_id = isset($_POST['parent_id']) ? _POST('parent_id') : 0;

        $reports_id = _POST('reports_id');

        if (!isset($app_selected_items[$reports_id])) {
            $app_selected_items[$reports_id] = [];
        }

        if (count($app_selected_items[$reports_id]) and !in_array($parent_id, $app_selected_items[$reports_id])) {
            db_query(
                "update app_entity_{$current_entity_id} set parent_id={$parent_id} where id in (" . implode(
                    ',',
                    $app_selected_items[$reports_id]
                ) . ")"
            );
        }

        redirect_to('items/items', 'path=' . $app_path);
    }
}