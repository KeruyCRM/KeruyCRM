<?php

namespace Tools\FieldsTypes;

class Fieldtype_access_group
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::f3()->TEXT_FIELDTYPE_ACCESS_GROUP_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::f3()->TEXT_WIDTH,
            'name' => 'width',
            'type' => 'dropdown',
            'choices' => [
                'input-small' => \K::f3()->TEXT_INPUT_SMALL,
                'input-medium' => \K::f3()->TEXT_INPUT_MEDIUM,
                'input-large' => \K::f3()->TEXT_INPUT_LARGE,
                'input-xlarge' => \K::f3()->TEXT_INPUT_XLARGE
            ],
            'tooltip_icon' => \K::f3()->TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[] = [
            'title' => \K::f3()->TEXT_DISPLAY_USERS_AS,
            'name' => 'display_as',
            'type' => 'dropdown',
            'choices' => [
                'dropdown' => \K::f3()->TEXT_DISPLAY_USERS_AS_DROPDOWN,
                'checkboxes' => \K::f3()->TEXT_DISPLAY_USERS_AS_CHECKBOXES,
                'dropdown_muliple' => \K::f3()->TEXT_DISPLAY_USERS_AS_DROPDOWN_MULTIPLE
            ],
            'params' => ['class' => 'form-control input-xlarge']
        ];

        $cfg[] = [
            'title' => \K::f3()->TEXT_DEFAULT_TEXT,
            'name' => 'default_text',
            'type' => 'input',
            'tooltip_icon' => \K::f3()->TEXT_DEFAULT_TEXT_INFO,
            'params' => ['class' => 'form-control input-large']
        ];

        $cfg[] = [
            'title' => \K::f3()->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::f3()->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $cfg[] = [
            'title' => \K::f3()->TEXT_USERS_GROUPS,
            'tooltip_icon' => \K::f3()->TEXT_FIELDTYPE_ACCESS_GROUP_USERS_GROUP_TIP,
            'name' => 'use_groups',
            'type' => 'dropdown',
            'choices' => access_groups::get_choices(false),
            'params' => ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
        ];

        $cfg[] = ['title' => \K::f3()->TEXT_HIDE_ADMIN, 'name' => 'hide_admin', 'type' => 'checkbox'];

        $cfg[] = [
            'title' => \K::f3()->TEXT_SEND_NOTIFICATION,
            'tooltip_icon' => \K::f3()->TEXT_FIELDTYPE_ACCESS_GROUP_NOTIFY_TIP,
            'name' => 'send_notification',
            'type' => 'checkbox'
        ];

        return $cfg;
    }

    public static function get_choices($field, $value = '')
    {
        global $app_user;

        $cfg = new fields_types_cfg($field['configuration']);

        $choices = [];

        if ($cfg->get('hide_admin') != 1) {
            $choices[0] = \K::f3()->TEXT_ADMINISTRATOR;
        }

        $where_sql = "where (select count(*) from app_entities_access ea where ea.access_groups_id=ag.id and entities_id='" . $field['entities_id'] . "' and length(access_schema))>0";

        $where_sql .= (is_array($cfg->get('use_groups')) ? " and ag.id in (" . implode(
                ',',
                $cfg->get('use_groups')
            ) . (strlen($value) ? ',' . $value : '') . ")" : "");

        $groups_query = db_query("select ag.* from app_access_groups ag {$where_sql} order by ag.sort_order, ag.name");
        while ($groups = db_fetch_array($groups_query)) {
            $choices[$groups['id']] = $groups['name'];
        }

        return $choices;
    }

    public function render($field, $obj, $params = [])
    {
        global $app_users_cache, $app_user;

        $cfg = new fields_types_cfg($field['configuration']);

        $entities_id = $field['entities_id'];

        $value = (strlen($obj['field_' . $field['id']]) ? $obj['field_' . $field['id']] : '');

        $choices = self::get_choices($field, $value);

        if ($cfg->get('display_as') == 'dropdown') {
            //add empty value for comment form
            $choices = ($params['form'] == 'comment' ? ['' => ''] + $choices : $choices);

            $attributes = [
                'class' => 'form-control chosen-select ' . $cfg->get(
                        'width'
                    ) . ' field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')
            ];

            return select_tag(
                    'fields[' . $field['id'] . ']',
                    [
                        '' => (strlen($cfg->get('default_text')) ? $cfg->get('default_text') : \K::f3()->TEXT_NONE)
                    ] + $choices,
                    $value,
                    $attributes
                ) . fields_types::custom_error_handler($field['id']);
        } elseif ($cfg->get('display_as') == 'checkboxes') {
            $attributes = ['class' => 'field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')];

            return '<div class="checkboxes_list ' . ($field['is_required'] == 1 ? ' required' : '') . '">' . select_checkboxes_tag(
                    'fields[' . $field['id'] . ']',
                    $choices,
                    $value,
                    $attributes
                ) . '</div>';
        } elseif ($cfg->get('display_as') == 'dropdown_muliple') {
            $attributes = [
                'class' => 'form-control ' . $cfg->get(
                        'width'
                    ) . ' chosen-select field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : ''),
                'multiple' => 'multiple',
                'data-placeholder' => ($cfg->get('default_text') ? $cfg->get('default_text') : \K::f3(
                )->TEXT_SELECT_SOME_VALUES)
            ];
            return select_tag(
                    'fields[' . $field['id'] . '][]',
                    $choices,
                    $value,
                    $attributes
                ) . fields_types::custom_error_handler($field['id']);
        }
    }

    public function process($options)
    {
        global $app_send_to;

        $cfg = new fields_types_cfg($options['field']['configuration']);

        $value = (is_array($options['value']) ? implode(',', $options['value']) : $options['value']);

        //send notification
        if ($cfg->get('send_notification') == 1 and strlen($value)) {
            $users_query = db_query("select id from app_entity_1 where field_6 in (" . $value . ") and field_5=1");
            while ($users = db_fetch_array($users_query)) {
                $app_send_to[] = $users['id'];
            }
        }

        return $value;
    }

    public function output($options)
    {
        $is_export = isset($options['is_export']);

        if (!strlen($options['value'])) {
            return '';
        }

        $names = [];
        foreach (explode(',', $options['value']) as $id) {
            $names[] = access_groups::get_name_by_id($id);
        }

        return ($is_export ? implode(', ', $names) : implode('<br>', $names));
    }

    public function reports_query($options)
    {
        global $app_user;

        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $prefix = (strlen($options['prefix']) ? $options['prefix'] : 'e');

        if (strlen($filters['filters_values']) > 0) {
            $filters['filters_values'] = str_replace(
                'current_user_group_id',
                $app_user['group_id'],
                $filters['filters_values']
            );

            $sql_query[] = "(select count(*) from app_entity_" . $options['entities_id'] . "_values as cv where cv.items_id=" . $prefix . ".id and cv.fields_id='" . db_input(
                    $options['filters']['fields_id']
                ) . "' and cv.value in (" . $filters['filters_values'] . ")) " . ($filters['filters_condition'] == 'include' ? '>0' : '=0');
        }

        return $sql_query;
    }

    public static function get_send_to($value)
    {
        if (!strlen($value)) {
            return [];
        }

        $send_to = [];

        $users_query = db_query("select id from app_entity_1 where field_6 in (" . $value . ") and field_5=1");
        while ($users = db_fetch_array($users_query)) {
            $send_to[] = $users['id'];
        }

        return $send_to;
    }
}
