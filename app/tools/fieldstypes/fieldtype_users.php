<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_users
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_USERS_TITLE];
    }

    public function get_configuration($params = [])
    {
        $entity_info = \K::model()->db_find('app_entities', $params['entities_id']);

        $cfg = [];
        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_DISPLAY_USERS_AS,
            'name' => 'display_as',
            'type' => 'dropdown',
            'params' => ['class' => 'form-control input-xlarge'],
            'choices' => [
                'dropdown' => \K::$fw->TEXT_DISPLAY_USERS_AS_DROPDOWN,
                'checkboxes' => \K::$fw->TEXT_DISPLAY_USERS_AS_CHECKBOXES,
                'dropdown_multiple' => \K::$fw->TEXT_DISPLAY_USERS_AS_DROPDOWN_MULTIPLE
            ]
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_NAME,
            'name' => 'hide_field_name',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_NAME_TIP
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_DISABLE_NOTIFICATIONS,
            'name' => 'disable_notification',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_DISABLE_NOTIFICATIONS_FIELDS_INFO
        ];

        if ($entity_info['parent_id'] > 0) {
            $cfg[\K::$fw->TEXT_SETTINGS][] = [
                'title' => \K::$fw->TEXT_DISABLE_USERS_DEPENDENCY,
                'name' => 'disable_dependency',
                'type' => 'checkbox',
                'tooltip_icon' => \K::$fw->TEXT_DISABLE_USERS_DEPENDENCY_INFO
            ];
        }

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HIDE_ADMIN,
            'name' => 'hide_admin',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_AUTHORIZED_USER_BY_DEFAULT,
            'name' => 'authorized_user_by_default',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_AUTHORIZED_USER_BY_DEFAULT_INFO
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_ALLOW_SEARCH_TIP
        ];

        $cfg[\K::$fw->TEXT_EXTRA][] = [
            'title' => \K::$fw->TEXT_HIDE_ACCESS_GROUP,
            'name' => 'hide_access_group',
            'type' => 'checkbox'
        ];
        $cfg[\K::$fw->TEXT_EXTRA][] = [
            'title' => \K::$fw->TEXT_USERS_GROUPS,
            'name' => 'use_groups',
            'type' => 'dropdown',
            'choices' => \Models\Main\Access_groups::get_choices(false),
            'tooltip_icon' => \K::$fw->TEXT_USE_GROUPS_TIP,
            'params' => ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
        ];

        return $cfg;
    }

    public static function get_choices($field, $params, $value = '')
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $entities_id = $field['entities_id'];

        //get access schema
        $access_schema = \Models\Main\Users\Users::get_entities_access_schema_by_groups($entities_id);

        //check if parent item has users fields and if users are assigned
        $has_parent_users = false;
        $parent_users_list = [];

        if (isset($params['parent_entity_item_id']) and $params['parent_entity_item_id'] > 0 and $cfg->get(
                'disable_dependency'
            ) != 1) {
            if ($parent_users_list = \Models\Main\Items\Items::get_parent_users_list(
                $entities_id,
                $params['parent_entity_item_id']
            )) {
                $has_parent_users = true;
            }
        }

        //get users choices
        //select all active users or already assigned users
        $where_sql = (strlen($value) ? "(u.field_5 = 1 or u.id in (" . $value . "))" : "u.field_5 = 1");

        $choices = [];
        $order_by_sql = ($cfg->get('hide_access_group') != 1 ? 'group_name,' : '');
        $order_by_sql .= (\K::$fw->CFG_APP_DISPLAY_USER_NAME_ORDER == 'firstname_lastname' ? ' u.field_7, u.field_8' : ' u.field_8, u.field_7');

        $users_query = \K::model()->db_query_exec(
            "select u.*, a.name as group_name from app_entity_1 u left join app_access_groups a on a.id = u.field_6 where {$where_sql} order by " . $order_by_sql,
            null,
            'app_entity_1,app_access_groups'
        );

        //while ($users = db_fetch_array($users_query)) {
        foreach ($users_query as $users) {
            $multiple_access_groups = strlen($users['multiple_access_groups']) ? explode(
                ',',
                $users['multiple_access_groups']
            ) : [$users['field_6']];

            foreach ($multiple_access_groups as $access_group_id) {
                //hide administrators
                if ($cfg->get('hide_admin') == 1 and $access_group_id == 0) {
                    continue;
                }

                //display users from selected users groups only
                if (is_array($cfg->get('use_groups')) and count($cfg->get('use_groups')) and !in_array(
                        $access_group_id,
                        $cfg->get('use_groups')
                    )) {
                    continue;
                }

                if (!isset($access_schema[$access_group_id])) {
                    $access_schema[$access_group_id] = [];
                }

                if ($access_group_id == 0 or in_array('view', $access_schema[$access_group_id]) or in_array(
                        'view_assigned',
                        $access_schema[$access_group_id]
                    )) {
                    //check parent users and check already assigned
                    if ($has_parent_users and !in_array($users['id'], $parent_users_list) and !in_array(
                            $users['id'],
                            explode(',', $value)
                        )) {
                        continue;
                    }

                    $group_name = (strlen($access_group_id) > 0 ? \Models\Main\Access_groups::get_name_by_id(
                        $access_group_id
                    ) : \K::$fw->TEXT_ADMINISTRATOR);

                    if ($cfg->get('hide_access_group') == 1) {
                        $choices[$users['id']] = \K::$fw->app_users_cache[$users['id']]['name'];
                    } else {
                        $choices[$group_name][$users['id']] = \K::$fw->app_users_cache[$users['id']]['name'];
                    }

                    //break from foreach to add only one user in list
                    break;
                }
            }
        }

        return $choices;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $value = '';

        if (strlen($obj['field_' . $field['id']])) {
            $value = $obj['field_' . $field['id']];
        } elseif ($cfg->get('authorized_user_by_default') == 1) {
            $value = \K::$fw->app_user['id'];
        }

        $choices = self::get_choices($field, $params, $value);

        if ($cfg->get('display_as') == 'dropdown') {
            //add empty value for comment form
            $choices = ($params['form'] == 'comment' ? ['' => ''] + $choices : $choices);

            $attributes = ['class' => 'form-control chosen-select input-large field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')];

            return \Helpers\Html::select_tag(
                    'fields[' . $field['id'] . ']',
                    ['' => \K::$fw->TEXT_NONE] + $choices,
                    $value,
                    $attributes
                ) . \Models\Main\Fields_types::custom_error_handler($field['id']);
        } elseif ($cfg->get('display_as') == 'checkboxes') {
            $attributes = ['class' => 'field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')];

            return '<div class="checkboxes_list ' . ($field['is_required'] == 1 ? ' required' : '') . '">' . \Helpers\Html::select_checkboxes_tag(
                    'fields[' . $field['id'] . ']',
                    $choices,
                    $value,
                    $attributes
                ) . '</div>';
        } elseif ($cfg->get('display_as') == 'dropdown_multiple') {
            $attributes = [
                'class' => 'form-control input-xlarge chosen-select field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : ''),
                'multiple' => 'multiple',
                'data-placeholder' => \K::$fw->TEXT_SELECT_SOME_VALUES
            ];
            return \Helpers\Html::select_tag(
                    'fields[' . $field['id'] . '][]',
                    $choices,
                    explode(',', $value),
                    $attributes
                ) . \Models\Main\Fields_types::custom_error_handler($field['id']);
        }
    }

    public function process($options)
    {
        global $app_send_to, $app_send_to_new_assigned;

        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        if ($cfg->get('disable_notification') != 1) {
            if (is_array($options['value'])) {
                \K::$fw->app_send_to = array_merge($options['value'], \K::$fw->app_send_to);
            } else {
                \K::$fw->app_send_to[] = $options['value'];
            }
        }

        $value = (is_array($options['value']) ? implode(',', $options['value']) : $options['value']);

        //check if value changed
        if ($cfg->get('disable_notification') != 1) {
            if (!$options['is_new_item']) {
                if ($value != $options['current_field_value']) {
                    $array = array_diff(explode(',', $value), explode(',', $options['current_field_value']));

                    \K::$fw->app_send_to_new_assigned = array_merge(\K::$fw->app_send_to_new_assigned, $array);
                    /*foreach (array_diff(explode(',', $value), explode(',', $options['current_field_value'])) as $v) {
                        \K::$fw->app_send_to_new_assigned[] = $v;
                    }*/
                }
            }
        }

        return $value;
    }

    public function output($options)
    {
        $users_list = [];
        $exp = explode(',', $options['value']);

        if (isset($options['is_export'])) {
            foreach ($exp as $id) {
                if (isset(\K::$fw->app_users_cache[$id])) {
                    $users_list[] = \K::$fw->app_users_cache[$id]['name'];
                }
            }

            return implode(', ', $users_list);
        } else {
            foreach ($exp as $id) {
                if (isset(\K::$fw->app_users_cache[$id])) {
                    if (isset($options['display_user_photo'])) {
                        $photo = '<div class="user-photo-box">' . \Helpers\App::render_user_photo(
                                \K::$fw->app_users_cache[$id]['photo']
                            ) . '</div>';
                        $is_photo_display = true;
                    } else {
                        $photo = '';
                        $is_photo_display = false;
                    }

                    $users_list[] = $photo . ' <span class="user-name" ' . \Models\Main\Users\Users::render_public_profile(
                            \K::$fw->app_users_cache[$id],
                            $is_photo_display
                        ) . '>' . \K::$fw->app_users_cache[$id]['name'] . '</span> <div style="clear:both"></div>';
                }
            }

            return implode('', $users_list);
        }
    }

    public function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $prefix = (strlen($options['prefix']) ? $options['prefix'] : 'e');

        if (strlen($filters['filters_values']) > 0) {
            $filters['filters_values'] = str_replace(
                'current_user_id',
                \K::$fw->app_user['id'],
                $filters['filters_values']
            );

            $sql_query[] = "(select count(*) from app_entity_" . (int)$options['entities_id'] . "_values as cv where cv.items_id = {$prefix}.id and cv.fields_id = " . (int)$options['filters']['fields_id'] . " and cv.value in (" . $filters['filters_values'] . ")) " . ($filters['filters_condition'] == 'include' ? ' > 0' : ' = 0');
        }

        return $sql_query;
    }
}