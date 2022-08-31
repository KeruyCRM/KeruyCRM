<?php

namespace Models\Main\Users;

class User_roles
{
    public static function get_access_by_role($entities_id, $items_id, $check_entities_id = false)
    {
        //admin have full access
        if (\K::$fw->app_user['group_id'] == 0) {
            return false;
        }

        //get items full path
        $path_array = \Models\Main\Items\Items::get_path_array($entities_id, $items_id);

        $fields_list = [];
        //search entity where there is fieldtype_user_roles
        foreach ($path_array as $info) {
            $fields_list = [];
            /*$fields_query = db_query(
                "select id from app_fields where entities_id='" . $info['entities_id'] . "' and type='fieldtype_user_roles'"
            );*/

            $fields_query = \K::model()->db_fetch('app_fields', [
                'entities_id = ? and type = ?',
                $info['entities_id'],
                'fieldtype_user_roles'
            ], [], 'id');

            //while ($fields = db_fetch_array($fields_query)) {
            foreach ($fields_query as $fields) {
                $fields = $fields->cast();

                $fields_list[] = $fields['id'];
            }

            if (count($fields_list)) {
                break;
            }
        }

        //exit if no any roles
        if (!count($fields_list)) {
            return false;
        }

        $roles_info = false;

        //check if there is assigned roles to item
        /*$assigend_role_query = db_query(
            "select * from app_user_roles_to_items where fields_id in (" . implode(
                ',',
                $fields_list
            ) . ") and entities_id='" . $info['entities_id'] . "' and items_id='" . $info['items_id'] . "' and users_id='" . \K::$fw->app_user['id'] . "'"
        );*/

        $info = array_pop($path_array);//FIX undefined, get last value
        //TODO Really get only last access?

        $assigned_role = \K::model()->db_fetch_one('app_user_roles_to_items', [
            'fields_id in (' . \K::model()->quoteToString(
                $fields_list
            ) . ') and entities_id = ? and items_id = ? and users_id = ?',
            $info['entities_id'],
            $info['items_id'],
            \K::$fw->app_user['id']
        ], [], 'fields_id,roles_id');

        if ($assigned_role) {
            $roles_entities_access = [];
            $roles_entities_comments_access = [];
            $roles_entities_fields_access = [];

            //get roles access
            /*$roles_access_query = db_query(
                "select * from app_user_roles_access where fields_id='" . $assigned_role['fields_id'] . "' and user_roles_id='" . $assigned_role['roles_id'] . "'"
            );*/

            $roles_access_query = \K::model()->db_fetch('app_user_roles_access', [
                'fields_id = ? and user_roles_id = ?',
                $assigned_role['fields_id'],
                $assigned_role['roles_id']
            ]);

            //while ($roles_access = db_fetch_array($roles_access_query)) {
            foreach ($roles_access_query as $roles_access) {
                $roles_access = $roles_access->cast();

                $roles_entities_access[$roles_access['entities_id']] = (strlen(
                    $roles_access['access_schema']
                ) ? explode(',', $roles_access['access_schema']) : []);

                $roles_entities_comments_access[$roles_access['entities_id']] = (strlen(
                    $roles_access['comments_access']
                ) ? explode(',', $roles_access['comments_access']) : []);

                $roles_entities_fields_access[$roles_access['entities_id']] = (strlen(
                    $roles_access['fields_access']
                ) ? json_decode($roles_access['fields_access'], true) : []);
            }

            if (count($roles_entities_access)) {
                //global roles access for entities list
                $roles_info['roles_entities_access'] = $roles_entities_access;

                //to rewrite curren entity access
                if ($check_entities_id) {
                    foreach ($roles_entities_access as $entity_id => $access) {
                        if ($check_entities_id == $entity_id) {
                            $roles_info['current_access_schema'] = $access;
                            $roles_info['current_comments_access_schema'] = $roles_entities_comments_access[$entity_id];
                            $roles_info['fields_access_schema'] = $roles_entities_fields_access[$entity_id];
                        }
                    }
                }
            }
        }

        return $roles_info;
    }

    public static function get_role_to_items($fields_id, $entities_id, $items_id, $users_id)
    {
        $role_query = db_query(
            "select roles_id from app_user_roles_to_items where fields_id='" . $fields_id . "' and  entities_id='" . $entities_id . "' and items_id='" . $items_id . "' and users_id='" . $users_id . "'"
        );
        if ($role = db_fetch_array($role_query)) {
            return $role['roles_id'];
        } else {
            return false;
        }
    }

    public static function get_choices($fields_id, $add_empty = true)
    {
        $choices = [];

        if ($add_empty) {
            $choices[''] = '';
        }

        $roles_query = db_query(
            "select * from app_user_roles where fields_id='" . db_input($fields_id) . "' order by sort_order, name"
        );
        while ($roles = db_fetch_array($roles_query)) {
            $choices[$roles['id']] = $roles['name'];
        }

        return $choices;
    }

    public static function get_name_by_id($id)
    {
        //$info_query = db_query("select * from app_user_roles where id='" . (int)$id . "'");

        $info = \K::model()->db_fetch_one('app_user_roles', [
            'id = ?',
            $id
        ], [], 'name');

        if ($info) {
            return $info['name'];
        }

        return '';
    }
}