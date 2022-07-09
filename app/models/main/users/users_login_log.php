<?php

namespace Models\Main\Users;

class Users_login_log
{
    public static function get_type_choiсes()
    {
        return ['' => '', '0' => \K::$fw->TEXT_LOGIN_ATTEMPT, '1' => \K::$fw->TEXT_SUCCESSFUL_LOGIN];
    }

    public static function success($username, $users_id)
    {
        self::log($username, 1, $users_id);
    }

    public static function fail($username)
    {
        self::log($username);
    }

    public static function log($username, $is_success = 0, $users_id = 0)
    {
        $sql_data = [
            'users_id' => $users_id,
            'username' => $username,
            'identifier' => \K::$fw->IP,// $_SERVER['REMOTE_ADDR'],
            'is_success' => $is_success,
            'date_added' => time(),
        ];

        \K::model()->db_perform('app_users_login_log', $sql_data);

        if ($is_success and $users_id > 0) {
            self::set_user_last_login_date($users_id);
        }
    }

    public static function set_user_last_login_date($users_id)
    {
        //prepare fieldtype_user_last_login_date
        /*$fields_query = db_query(
            "select id, entities_id from app_fields where type in ('fieldtype_user_last_login_date') and  entities_id=1"
        );*/

        $fields = \K::model()->db_fetch_one(
            'app_fields',
            ['type = ? and entities_id = 1', 'fieldtype_user_last_login_date'],
            [],
            'id,entities_id'
        );

        if (!$fields) {
            $sql_data = [
                'type' => 'fieldtype_user_last_login_date',
                'entities_id' => 1,
                'forms_tabs_id' => 1,
            ];

            $mapper = \K::model()->db_perform('app_fields', $sql_data);

            $field_id = \K::model()->db_insert_id($mapper);

            \K::model()->db_query_exec("ALTER TABLE app_entity_1 ADD field_{$field_id} INT NOT NULL;");
        } else {
            $field_id = $fields['id'];
        }

        //update
        //db_query("update app_entity_1 set field_{$field_id}=" . time() . " where id={$users_id}");
        \K::model()->db_perform('app_entity_1', ["field_{$field_id}" => time()], ['id = ?', $users_id]);
    }

    static function delete_by_user_id($users_id)
    {
        //db_query("delete from app_users_login_log where users_id='" . $users_id . "'");
        \K::model()->db_delete_row('app_users_login_log', $users_id, 'users_id');
    }
}