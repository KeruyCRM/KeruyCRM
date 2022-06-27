<?php

namespace Tools\FieldsTypes;

class Fieldtype_created_by
{
    public $options;

    public function __construct()
    {
        $this->options = [
            'name' => \K::$fw->TEXT_FIELDTYPE_CREATEDBY_TITLE,
            'title' => \K::$fw->TEXT_FIELDTYPE_CREATEDBY_TITLE
        ];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DISABLE_NOTIFICATIONS,
            'name' => 'disable_notification',
            'type' => 'checkbox'
        ];

        return $cfg;
    }

    public function output($options)
    {
        global $app_users_cache;

        if ($options['field']['entities_id'] == 1 and $options['value'] == 0) {
            return \K::$fw->TEXT_PUBLIC_REGISTRATION;
        } elseif (isset($options['is_export']) and isset($app_users_cache[$options['value']])) {
            return $app_users_cache[$options['value']]['name'];
        } elseif (isset($app_users_cache[$options['value']])) {
            return '<span ' . users::render_public_profile(
                    $app_users_cache[$options['value']]
                ) . '>' . $app_users_cache[$options['value']]['name'] . '</span>';
        } else {
            return '';
        }
    }

    public function process($options)
    {
        return $options['value'];
    }

    public function render($field, $obj, $params = [])
    {
        global $app_users_cache;

        $access_schema = users::get_entities_access_schema_by_groups($field['entities_id']);

        $choices = [];
        $order_by_sql = (
        \K::$fw->CFG_APP_DISPLAY_USER_NAME_ORDER == 'firstname_lastname' ? 'u.field_7, u.field_8' : 'u.field_8, u.field_7');
        $users_query = db_query(
            "select u.*,a.name as group_name from app_entity_1 u left join app_access_groups a on a.id=u.field_6 where u.field_5=1 order by group_name, " . $order_by_sql
        );
        while ($users = db_fetch_array($users_query)) {
            if (!isset($access_schema[$users['field_6']])) {
                $access_schema[$users['field_6']] = [];
            }

            if ($users['field_6'] == 0 or in_array('view', $access_schema[$users['field_6']]) or in_array(
                    'view_assigned',
                    $access_schema[$users['field_6']]
                )) {
                $group_name = (strlen($users['group_name']) > 0 ? $users['group_name'] : \K::$fw->TEXT_ADMINISTRATOR);
                $choices[$group_name][$users['id']] = $app_users_cache[$users['id']]['name'];
            }
        }

        $value = (strlen($obj['field_' . $field['id']]) ? $obj['field_' . $field['id']] : '');

        $attributes = ['class' => 'form-control chosen-select input-large field_' . $field['id']];

        return select_tag('fields[' . $field['id'] . ']', $choices, $value, $attributes);
    }

    public function reports_query($options)
    {
        global $app_user;

        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $sql = [];

        if (strlen($filters['filters_values']) > 0) {
            $filters['filters_values'] = str_replace('current_user_id', $app_user['id'], $filters['filters_values']);

            $sql_query[] = "(e.created_by " . ($filters['filters_condition'] == 'include' ? 'in' : 'not in') . " (" . $filters['filters_values'] . "))";
        }

        return $sql_query;
    }

    public static function is_notification_enabled($entities_id)
    {
        global $app_fields_cache;

        $cfg = new fields_types_cfg($app_fields_cache[$entities_id]['fieldtype_created_by']['configuration']);

        return ($cfg->get('disable_notification') == 1 ? false : true);
    }
}