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
        \K::$fw->count_selected_text = sprintf(\K::$fw->TEXT_SELECTED_RECORDS, count(\K::$fw->app_selected_items[\K::$fw->GET['reports_id']]));

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'change_parent_selected.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }

    public function change_parent()
    {
        if (\K::$fw->VERB == 'POST') {
            $parent_id = \K::$fw->POST['parent_id'] ?? 0;

            $reports_id = \K::$fw->POST['reports_id'];

            if (!isset(\K::$fw->app_selected_items[$reports_id])) {
                \K::$fw->app_selected_items[$reports_id] = [];
            }

            if (count(\K::$fw->app_selected_items[$reports_id]) and !in_array(
                    $parent_id,
                    \K::$fw->app_selected_items[$reports_id]
                )) {
                /*db_query(
                    "update app_entity_{$current_entity_id} set parent_id={$parent_id} where id in (" . implode(
                        ',',
                        $app_selected_items[$reports_id]
                    ) . ")"
                );*/

                \K::model()->db_update('app_entity_' . (int)\K::$fw->current_entity_id, ['parent_id' => $parent_id], [
                    'id in (' . \K::model()->quoteToString(
                        \K::$fw->app_selected_items[$reports_id],
                        \PDO::PARAM_INT
                    ) . ')'
                ]);
            }

            \Helpers\Urls::redirect_to('main/items/items', 'path=' . \K::$fw->app_path);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}