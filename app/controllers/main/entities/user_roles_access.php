<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class User_roles_access extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();

        \K::$fw->field_info = \K::model()->db_find('app_fields', \K::$fw->GET['fields_id']);

        \K::$fw->user_roles_info = \K::model()->db_find('app_user_roles', \K::$fw->GET['role_id']);

        \K::$fw->cfg = new \Models\Main\Fields_types_cfg(\K::$fw->field_info['configuration']);
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'user_roles_access.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['role_id'])) {
            if (isset(\K::$fw->POST['entities'])) {
                \K::model()->begin();

                foreach (\K::$fw->POST['entities'] as $entities_id) {
                    $access_schema = \K::$fw->POST['access'][$entities_id];

                    $access_schema = \Models\Main\Access_groups::prepare_entities_access_schema($access_schema);

                    $comments_access = '';
                    if (isset(\K::$fw->POST['comments_access'][$entities_id])) {
                        $comments_access = str_replace('_', ',', \K::$fw->POST['comments_access'][$entities_id]);
                    }

                    $sql_data = [
                        'user_roles_id' => \K::$fw->GET['role_id'],
                        'fields_id' => \K::$fw->GET['fields_id'],
                        'entities_id' => $entities_id,
                        'access_schema' => implode(',', $access_schema),
                        'comments_access' => $comments_access,
                    ];

                    //check if access exit
                    /*$roles_access_query = db_query(
                        "select id from app_user_roles_access where user_roles_id='" . \K::$fw->GET['role_id'] . "' and fields_id='" . \K::$fw->GET['fields_id'] . "' and entities_id='" . $entities_id . "'"
                    );
                    if ($roles_access = db_fetch_array($roles_access_query)) {
                        db_perform('app_user_roles_access', $sql_data, 'update', "id='" . $roles_access['id'] . "'");
                    } else {
                        db_perform('app_user_roles_access', $sql_data);
                    }*/

                    \K::model()->db_perform('app_user_roles_access', $sql_data, [
                        'user_roles_id = ? and fields_id = ? and entities_id = ?',
                        \K::$fw->GET['role_id'],
                        \K::$fw->GET['fields_id'],
                        $entities_id
                    ]);
                }

                /*db_query(
                    "delete from app_user_roles_access where user_roles_id='" . \K::$fw->GET['role_id'] . "' and fields_id='" . \K::$fw->GET['fields_id'] . "' and entities_id not in (" . implode(
                        ',',
                        \K::$fw->POST['entities']
                    ) . ")"
                );*/

                $notIn = \K::model()->quoteToString(\K::$fw->POST['entities'], \PDO::PARAM_INT);

                \K::model()->db_delete('app_user_roles_access', [
                    'user_roles_id = ? and fields_id = ? and entities_id not in (' . $notIn . ')',
                    \K::$fw->GET['role_id'],
                    \K::$fw->GET['fields_id']
                ]);

                \K::model()->commit();
            } else {
                //reset access for current role
                \K::model()->db_delete_row('app_user_roles_access', \K::$fw->GET['role_id'], 'user_roles_id');
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function set_fields_access()
    {
        if (\K::$fw->VERB == 'POST') {
            $fields_access = [];

            if (isset(\K::$fw->POST['access'])) {
                foreach (\K::$fw->POST['access'] as $fields_id => $access) {
                    if (in_array($access, ['view', 'hide'])) {
                        $fields_access[$fields_id] = $access;
                    }
                }
            }

            /*db_query(
                "update app_user_roles_access set fields_access='" . json_encode(
                    $fields_access
                ) . "' where user_roles_id='" . \K::$fw->GET['role_id'] . "' and entities_id='" . \K::$fw->GET['role_entities_id'] . "' and fields_id='" . \K::$fw->GET['fields_id'] . "'"
            );*/

            \K::model()->db_update('app_user_roles_access', ['fields_access' => json_encode($fields_access)], [
                'user_roles_id = ? and entities_id = ? and fields_id = ?',
                \K::$fw->GET['role_id'],
                \K::$fw->GET['role_entities_id'],
                \K::$fw->GET['fields_id']
            ]);

            \Helpers\Urls::redirect_to(
                'main/entities/user_roles_access',
                'role_id=' . \K::$fw->GET['role_id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}