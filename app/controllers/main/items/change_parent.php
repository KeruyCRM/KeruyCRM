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

        echo \K::view()->render(\K::$fw->subTemplate);
    }

    public function change_parent()
    {
        if (\K::$fw->VERB == 'POST') {
            $parent_id = \K::$fw->POST['parent_id'] ?? 0;

            if ($parent_id != \K::$fw->current_item_id) {
                /*db_query(
                    "update app_entity_{$current_entity_id} set parent_id={$parent_id} where id={$current_item_id}"
                );*/

                \K::model()->db_update('app_entity_' . (int)\K::$fw->current_entity_id, ['parent_id' => $parent_id], [
                    'id = ?',
                    \K::$fw->current_item_id
                ]);
            }

            \Helpers\Urls::redirect_to('main/items/info', 'path=' . \K::$fw->app_path);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}