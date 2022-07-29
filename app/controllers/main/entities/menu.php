<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Menu extends \Controller
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
        \K::$fw->countMenu = \K::model()->db_count('app_entities_menu');

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'menu.php';

        echo \K::view()->render($this->app_layout);
    }

    public function sort()
    {
        if (isset(\K::$fw->POST['sort_items'])) {
            $sort_order = 0;
            foreach (explode(',', \K::$fw->POST['sort_items']) as $v) {
                /*db_query(
                    "update app_entities_menu set sort_order='" . $sort_order . "' where id='" . str_replace(
                        'item_',
                        '',
                        $v
                    ) . "'"
                );*/

                \K::model()->db_update(
                    'app_entities_menu',
                    ['sort_order' => $sort_order],
                    ['id = ?', str_replace('item_', '', $v)]
                );

                $sort_order++;
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function sort_items()
    {
        if (isset(\K::$fw->POST['sort_items'])) {
            /*db_query(
                "update app_entities_menu set entities_list='" . str_replace(
                    'item_',
                    '',
                    \K::$fw->POST['sort_items']
                ) . "' where id='" . db_input(\K::$fw->GET['id']) . "'",
                true
            );*/

            \K::model()->db_update('app_entities_menu', [
                'entities_list' => str_replace(
                    'item_',
                    '',
                    \K::$fw->POST['sort_items']
                )
            ], ['id = ?', \K::$fw->GET['id']]);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $sql_data = [
                'name' => \K::model()->db_prepare_input(\K::$fw->POST['name']),
                'icon' => \K::model()->db_prepare_input(\K::$fw->POST['icon']),
                'icon_color' => \K::model()->db_prepare_input(\K::$fw->POST['icon_color']),
                'bg_color' => \K::model()->db_prepare_input(\K::$fw->POST['bg_color']),
                'entities_list' => (isset(\K::$fw->POST['entities_list']) ? implode(
                    ',',
                    \K::$fw->POST['entities_list']
                ) : ''),
                'reports_list' => (isset(\K::$fw->POST['reports_list']) ? implode(
                    ',',
                    \K::$fw->POST['reports_list']
                ) : ''),
                'pages_list' => (isset(\K::$fw->POST['pages_list']) ? implode(',', \K::$fw->POST['pages_list']) : ''),
                'sort_order' => \K::model()->db_prepare_input(\K::$fw->POST['sort_order']),
                'type' => \K::model()->db_prepare_input(\K::$fw->POST['type']),
                'url' => \K::model()->db_prepare_input(\K::$fw->POST['url']),
                'users_groups' => (isset(\K::$fw->POST['users_groups']) ? implode(
                    ',',
                    \K::$fw->POST['users_groups']
                ) : ''),
                'assigned_to' => (isset(\K::$fw->POST['assigned_to']) ? implode(
                    ',',
                    \K::$fw->POST['assigned_to']
                ) : ''),
                'parent_id' => \K::$fw->POST['parent_id'],
            ];

            \K::model()->db_perform('app_entities_menu', $sql_data, ['id = ?', \K::$fw->GET['id']]);

            \Helpers\Urls::redirect_to('main/entities/menu');
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['id'])) {
            $obj = \K::model()->db_find('app_entities_menu', \K::$fw->GET['id']);

            \K::model()->db_delete_row('app_entities_menu', \K::$fw->GET['id']);

            //db_query("update app_entities_menu set parent_id=0 where parent_id='" . _get::int('id') . "'");

            \K::model()->db_update('app_entities_menu', ['parent_id' => 0], ['parent_id = ?', \K::$fw->GET['id']]);

            \K::flash()->addMessage(sprintf(\K::$fw->TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

            \Helpers\Urls::redirect_to('main/entities/menu');
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}