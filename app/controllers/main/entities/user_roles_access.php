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

        $field_info = db_find('app_fields', _get::int('fields_id'));

        $user_roles_info = db_find('app_user_roles', _get::int('role_id'));

        $cfg = new fields_types_cfg($field_info['configuration']);
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'user_roles_access.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        if (isset($_POST['entities'])) {
            foreach ($_POST['entities'] as $entities_id) {
                $access_schema = $_POST['access'][$entities_id];

                $access_schema = access_groups::prepare_entities_access_schema($access_schema);

                $comments_access = '';
                if (isset($_POST['comments_access'][$entities_id])) {
                    $comments_access = str_replace('_', ',', $_POST['comments_access'][$entities_id]);
                }

                $sql_data = [
                    'user_roles_id' => _get::int('role_id'),
                    'fields_id' => _get::int('fields_id'),
                    'entities_id' => $entities_id,
                    'access_schema' => implode(',', $access_schema),
                    'comments_access' => $comments_access,
                ];

                //check if access exit
                $roles_access_query = db_query(
                    "select id from app_user_roles_access where user_roles_id='" . _get::int(
                        'role_id'
                    ) . "' and fields_id='" . _get::int('fields_id') . "' and entities_id='" . $entities_id . "'"
                );
                if ($roles_access = db_fetch_array($roles_access_query)) {
                    db_perform('app_user_roles_access', $sql_data, 'update', "id='" . $roles_access['id'] . "'");
                } else {
                    db_perform('app_user_roles_access', $sql_data);
                }
            }

            db_query(
                "delete from app_user_roles_access where user_roles_id='" . _get::int(
                    'role_id'
                ) . "' and fields_id='" . _get::int('fields_id') . "' and entities_id not in (" . implode(
                    ',',
                    $_POST['entities']
                ) . ")"
            );
        } else {
            //reset access for current role
            db_delete_row('app_user_roles_access', _get::int('role_id'), 'user_roles_id');
        }
    }

    public function set_fields_access()
    {
        $fields_access = [];

        if (isset($_POST['access'])) {
            foreach ($_POST['access'] as $fields_id => $access) {
                if (in_array($access, ['view', 'hide'])) {
                    $fields_access[$fields_id] = $access;
                }
            }
        }

        db_query(
            "update app_user_roles_access set fields_access='" . json_encode(
                $fields_access
            ) . "' where user_roles_id='" . _get::int('role_id') . "' and entities_id='" . _get::int(
                'role_entities_id'
            ) . "' and fields_id='" . _get::int('fields_id') . "'"
        );

        redirect_to(
            'entities/user_roles_access',
            'role_id=' . _get::int('role_id') . '&entities_id=' . _get::int('entities_id') . '&fields_id=' . _get::int(
                'fields_id'
            )
        );
    }
}