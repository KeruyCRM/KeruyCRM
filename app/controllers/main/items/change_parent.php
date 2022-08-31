<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Change_parent extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'change_parent.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function change_parent()
    {
        $parent_id = isset($_POST['parent_id']) ? _POST('parent_id') : 0;

        if ($parent_id != $current_item_id) {
            db_query("update app_entity_{$current_entity_id} set parent_id={$parent_id} where id={$current_item_id}");
        }

        redirect_to('items/info', 'path=' . $app_path);
    }
}