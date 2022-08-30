<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class User_roles extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();

        \K::$fw->field_info = \K::model()->db_find('app_fields', \K::$fw->GET['fields_id']);

        \K::$fw->cfg = new \Models\Main\Fields_types_cfg(\K::$fw->field_info['configuration']);
    }

    public function index()
    {
        /*$filters_query = db_query(
            "select * from app_user_roles where fields_id='" . db_input(
                \K::$fw->field_info['id']
            ) . "' order by sort_order, name"
        );*/

        \K::$fw->filters_query = \K::model()->db_fetch('app_user_roles', [
            'fields_id = ?',
            \K::$fw->field_info['id']
        ], ['order' => 'sort_order,name']);

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'user_roles.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['id'])) {
            $sql_data = [
                'fields_id' => \K::$fw->GET['fields_id'],
                'entities_id' => \K::$fw->GET['entities_id'],
                'name' => \K::$fw->POST['name'],
                'sort_order' => \K::$fw->POST['sort_order'],
            ];

            /*if (isset(\K::$fw->GET['id'])) {
                db_perform('app_user_roles', $sql_data, 'update', "id='" . db_input(\K::$fw->GET['id']) . "'");
            } else {
                db_perform('app_user_roles', $sql_data);
            }*/

            \K::model()->db_perform('app_user_roles', $sql_data, [
                'id = ?',
                \K::$fw->GET['id']
            ]);

            \Helpers\Urls::redirect_to(
                'main/entities/user_roles',
                'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['id'])) {
            //$info_query = db_query("select * from app_user_roles where id='" . \K::$fw->GET['id'] . "'");

            $info = \K::model()->db_fetch_one('app_user_roles', [
                'id = ?',
                \K::$fw->GET['id']
            ]);

            if ($info) {
                //db_query("delete from app_user_roles where id='" . db_input($info['id']) . "'");
                //db_query("delete from app_user_roles_access where user_roles_id='" . db_input($info['id']) . "'");

                \K::model()->db_delete_row('app_user_roles', $info['id']);
                \K::model()->db_delete_row('app_user_roles_access', $info['id'], 'user_roles_id');

                \K::flash()->addMessage(sprintf(\K::$fw->TEXT_WARN_DELETE_SUCCESS, $info['name']), 'success');
            }

            \Helpers\Urls::redirect_to(
                'main/entities/user_roles',
                'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function sort()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['fields_id'])) {
            $choices_sorted = \K::$fw->POST['choices_sorted'];

            if (strlen($choices_sorted) > 0) {
                $choices_sorted = json_decode(stripslashes($choices_sorted), true);

                \K::model()->begin();

                $sort_order = 0;
                foreach ($choices_sorted as $v) {
                    /*db_query(
                        "update app_user_roles set sort_order='" . $sort_order . "' where id='" . db_input(
                            $v['id']
                        ) . "' and fields_id='" . db_input(\K::$fw->GET['fields_id']) . "'"
                    );*/

                    \K::model()->db_update('app_user_roles', ['sort_order' => $sort_order], [
                        'id = ? and fields_id = ?',
                        $v['id'],
                        \K::$fw->GET['fields_id']
                    ]);

                    $sort_order++;
                }

                \K::model()->commit();
            }

            \Helpers\Urls::redirect_to(
                'main/entities/user_roles',
                'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}