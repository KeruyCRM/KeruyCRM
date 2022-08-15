<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Entities_groups extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        //$groups_query = db_query("select * from app_entities_groups order by sort_order, name");

        \K::$fw->groups_query = \K::model()->db_fetch('app_entities_groups', [], ['order' => 'sort_order, name']);
        \K::$fw->groups_query_count = count(\K::$fw->groups_query);

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'entities_groups.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $sql_data = [
                'name' => \K::$fw->POST['name'],
                'sort_order' => \K::$fw->POST['sort_order']
            ];

            \K::model()->db_perform('app_entities_groups', $sql_data, ['id = ?', \K::$fw->GET['id']]);

            \Helpers\Urls::redirect_to('main/entities/entities_groups');
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['id'])) {
            $name = \Models\Main\Entities_groups::get_name_by_id(\K::$fw->GET['id']);

            \Models\Main\Entities_groups::delete(\K::$fw->GET['id']);

            \K::flash()->addMessage(sprintf(\K::$fw->TEXT_WARN_DELETE_SUCCESS, $name), 'success');

            \Helpers\Urls::redirect_to('main/entities/entities_groups');
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function sort()
    {
        if (\K::$fw->VERB == 'POST') {
            $choices_sorted = \K::$fw->POST['choices_sorted'];

            if (strlen($choices_sorted) > 0) {
                $choices_sorted = json_decode(stripslashes($choices_sorted), true);

                $sort_order = 0;

                \K::model()->begin();
                foreach ($choices_sorted as $v) {
                    //db_query("update app_entities_groups set sort_order={$sort_order} where id={$v['id']}");

                    \K::model()->db_update('app_entities_groups', ['sort_order' => $sort_order], ['id = ?', $v['id']]);

                    $sort_order++;
                }
                \K::model()->commit();
            }

            \Helpers\Urls::redirect_to('main/entities/entities_groups');
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}