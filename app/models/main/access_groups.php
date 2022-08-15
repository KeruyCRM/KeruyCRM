<?php

namespace Models\Main;

class Access_groups
{
    public static function get_access_view_value($access_schema)
    {
        switch (true) {
            case in_array('action_with_assigned', $access_schema):
                $view_access = 'action_with_assigned';
                break;
            case in_array('view_assigned', $access_schema):
                $view_access = 'view_assigned';
                break;
            case in_array('view', $access_schema):
                $view_access = 'view';
                break;
            default:
                $view_access = '';
                break;
        }

        return $view_access;
    }

    public static function prepare_entities_access_schema($access_schema)
    {
        if ((in_array('view_assigned', $access_schema) or in_array(
                    'action_with_assigned',
                    $access_schema
                )) and !in_array('view', $access_schema)) {
            $access_schema[] = 'view';
        }

        //check with selected
        if (in_array('update_selected', $access_schema) and !in_array('update', $access_schema)) {
            $access_schema[] = 'update';
        }

        if (in_array('delete_selected', $access_schema) and !in_array('delete', $access_schema)) {
            $access_schema[] = 'delete';
        }

        if (in_array('delete_creator', $access_schema) and !in_array('delete', $access_schema)) {
            $access_schema[] = 'delete';
        }

        if (in_array('export_selected', $access_schema) and !in_array('export', $access_schema)) {
            $access_schema[] = 'export';
        }

        return $access_schema;
    }

    public static function get_access_view_choices()
    {
        $choices = [
            '' => \K::$fw->TEXT_NO,
            'view' => \K::$fw->TEXT_VIEW_ACCESS,
            'view_assigned' => \K::$fw->TEXT_VIEW_ASSIGNED_ACCESS,
            'action_with_assigned' => \K::$fw->TEXT_VIEW_ALL_ACTION_WIDTH_ASSIGNED_ACCESS,
        ];

        return $choices;
    }

    public static function get_access_choices()
    {
        $access_choices = [
            'create' => \K::$fw->TEXT_CREATE_ACCESS,
            'update' => \K::$fw->TEXT_UPDATE_ACCESS,
        ];

        //extra access available in extension
        if (\Helpers\App::is_ext_installed()) {
            $access_choices += [
                'update_selected' => \K::$fw->TEXT_UPDATE_SELECTED_ACCESS,
                'copy' => \K::$fw->TEXT_COPY_RECORDS,
                'move' => \K::$fw->TEXT_MOVE_RECORDS,
                'repeat' => \K::$fw->TEXT_EXT_REPEAT,
            ];
        }

        $access_choices += [
            'delete' => \K::$fw->TEXT_DELETE_ACCESS,
            'delete_selected' => \K::$fw->TEXT_DELETE_SELECTED_ACCESS,
            'delete_creator' => \K::$fw->TEXT_DELETE_BY_CREATOR_ONLY,
            'export' => \K::$fw->TEXT_EXPORT_ACCESS,
            'export_selected' => \K::$fw->TEXT_EXPORT_SELECTED_ACCESS,
            'import' => \K::$fw->TEXT_IMPORT,
            'reports' => \K::$fw->TEXT_REPORTS_CREATE_ACCESS,
        ];

        return $access_choices;
    }

    public static function get_ldap_default_group_id()
    {
        $group_info_query = db_query("select id from app_access_groups where is_ldap_default=1");
        if ($group_info = db_fetch_array($group_info_query)) {
            return $group_info['id'];
        } else {
            return false;
        }
    }

    public static function get_default_group_id()
    {
        //$group_info_query = db_query("select id from app_access_groups where is_default=1");

        $group_info = \K::model()->db_fetch_one('app_access_groups', [
            'is_default = 1'
        ], [], 'id');

        if ($group_info) {
            return $group_info['id'];
        } else {
            return false;
        }
    }

    public static function get_name_by_id($id)
    {
        if ($id == 0) {
            return \K::$fw->TEXT_ADMINISTRATOR;
        } elseif (isset(\K::$fw->app_access_groups_cache[$id])) {
            return \K::$fw->app_access_groups_cache[$id];
        } else {
            return '';
        }
    }

    public static function get_name_by_id_list($list)
    {
        if (!is_array($list)) {
            $list = explode(',', $list);
        }

        $users_groups = [];

        foreach ($list as $id) {
            $users_groups[] = self::get_name_by_id($id);
        }

        return $users_groups;
    }

    public static function check_before_delete($id)
    {
        $count_query = db_query(
            "select count(*) as total from app_entity_1 where field_6={$id} or find_in_set({$id},multiple_access_groups)"
        );
        $count = db_fetch_array($count_query);

        if ($count['total'] > 0) {
            return sprintf(\K::$fw->TEXT_ERROR_DELETE_USER_GROUP, $count['total']);
        } else {
            return '';
        }
    }

    public static function get_choices($include_administrator = true)
    {
        $choices = [];

        if ($include_administrator) {
            $choices[0] = \K::$fw->TEXT_ADMINISTRATOR;
        }

        $groups_query = \K::model()->db_fetch(
            'app_access_groups',
            [],
            ['order' => 'sort_order,name'],
            'id,name'
        );

        foreach ($groups_query as $v) {
            $v = $v->cast();

            $choices[$v['id']] = $v['name'];
        }

        return $choices;
    }

    public static function get_cache()
    {
        return self::get_choices();
    }
}