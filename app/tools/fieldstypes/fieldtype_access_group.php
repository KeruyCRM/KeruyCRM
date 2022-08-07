<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_access_group
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_ACCESS_GROUP_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_WIDTH,
            'name' => 'width',
            'type' => 'dropdown',
            'choices' => [
                'input-small' => \K::$fw->TEXT_INPUT_SMALL,
                'input-medium' => \K::$fw->TEXT_INPUT_MEDIUM,
                'input-large' => \K::$fw->TEXT_INPUT_LARGE,
                'input-xlarge' => \K::$fw->TEXT_INPUT_XLARGE
            ],
            'tooltip_icon' => \K::$fw->TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DISPLAY_USERS_AS,
            'name' => 'display_as',
            'type' => 'dropdown',
            'choices' => [
                'dropdown' => \K::$fw->TEXT_DISPLAY_USERS_AS_DROPDOWN,
                'checkboxes' => \K::$fw->TEXT_DISPLAY_USERS_AS_CHECKBOXES,
                'dropdown_multiple' => \K::$fw->TEXT_DISPLAY_USERS_AS_DROPDOWN_MULTIPLE
            ],
            'params' => ['class' => 'form-control input-xlarge']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DEFAULT_TEXT,
            'name' => 'default_text',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_DEFAULT_TEXT_INFO,
            'params' => ['class' => 'form-control input-large']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_USERS_GROUPS,
            'tooltip_icon' => \K::$fw->TEXT_FIELDTYPE_ACCESS_GROUP_USERS_GROUP_TIP,
            'name' => 'use_groups',
            'type' => 'dropdown',
            'choices' => \Models\Main\Access_groups::get_choices(false),
            'params' => ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
        ];

        $cfg[] = ['title' => \K::$fw->TEXT_HIDE_ADMIN, 'name' => 'hide_admin', 'type' => 'checkbox'];

        $cfg[] = [
            'title' => \K::$fw->TEXT_SEND_NOTIFICATION,
            'tooltip_icon' => \K::$fw->TEXT_FIELDTYPE_ACCESS_GROUP_NOTIFY_TIP,
            'name' => 'send_notification',
            'type' => 'checkbox'
        ];

        return $cfg;
    }

    public static function get_choices($field, $value = '')
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $choices = [];

        if ($cfg->get('hide_admin') != 1) {
            $choices[0] = \K::$fw->TEXT_ADMINISTRATOR;
        }

        $where_sql = 'where (select count(*) from app_entities_access ea where ea.access_groups_id = ag.id and entities_id = ' . (int)$field['entities_id'] . ' and length(access_schema)) > 0';

        $where_sql .= (is_array($cfg->get('use_groups')) ? ' and ag.id in (' .
            \K::model()->quoteToString($cfg->get('use_groups'), \PDO::PARAM_INT) .
            (strlen($value) ? ',' . (int)$value : '') . ')' : '');

        $groups_query = \K::model()->db_query_exec(
            "select ag.id, ag.name from app_access_groups ag {$where_sql} order by ag.sort_order, ag.name",
            null,
            'app_access_groups,app_entities_access'
        );

        //while ($groups = db_fetch_array($groups_query)) {
        foreach ($groups_query as $groups) {
            $choices[$groups['id']] = $groups['name'];
        }

        return $choices;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

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

            return \Helpers\Html::select_tag(
                    'fields[' . $field['id'] . ']',
                    [
                        '' => (strlen($cfg->get('default_text')) ? $cfg->get('default_text') : \K::$fw->TEXT_NONE)
                    ] + $choices,
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
                'class' => 'form-control ' . $cfg->get(
                        'width'
                    ) . ' chosen-select field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : ''),
                'multiple' => 'multiple',
                'data-placeholder' => ($cfg->get('default_text') ? $cfg->get(
                    'default_text'
                ) : \K::$fw->TEXT_SELECT_SOME_VALUES)
            ];
            return \Helpers\Html::select_tag(
                    'fields[' . $field['id'] . '][]',
                    $choices,
                    $value,
                    $attributes
                ) . \Models\Main\Fields_types::custom_error_handler($field['id']);
        }
    }

    public function process($options)
    {
        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        $value = (is_array($options['value']) ? \K::model()->quoteToString(
            $options['value'],
            \PDO::PARAM_INT
        ) : \K::model()->quote($options['value'], \PDO::PARAM_INT));

        //send notification
        if ($cfg->get('send_notification') == 1 and strlen($value)) {
            //$users_query = db_query("select id from app_entity_1 where field_6 in (" . $value . ") and field_5=1");

            $users_query = \K::model()->db_fetch('app_entity_1', [
                'field_6 in (' . $value . ') and field_5 = 1'
            ], [], 'id');

            //while ($users = db_fetch_array($users_query)) {
            foreach ($users_query as $users) {
                $users = $users->cast();

                \K::$fw->app_send_to[] = $users['id'];
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
            $names[] = \Models\Main\Access_groups::get_name_by_id($id);
        }

        return ($is_export ? implode(', ', $names) : implode('<br>', $names));
    }

    public function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $prefix = (strlen($options['prefix']) ? $options['prefix'] : 'e');

        if (strlen($filters['filters_values']) > 0) {
            $filters['filters_values'] = str_replace(
                'current_user_group_id',
                \K::$fw->app_user['group_id'],
                $filters['filters_values']
            );

            $sql_query[] = '(select count(*) from app_entity_' . (int)$options['entities_id'] . '_values as cv where cv.items_id = ' . $prefix . '.id and cv.fields_id = ' . (int)$options['filters']['fields_id']
                . ' and cv.value in (' . \K::model()->quoteToString(
                    $filters['filters_values'],
                    \PDO::PARAM_INT
                ) . ')) ' . ($filters['filters_condition'] == 'include' ? ' > 0' : ' = 0');
        }

        return $sql_query;
    }

    public static function get_send_to($value)
    {
        if (!strlen($value)) {
            return [];
        }

        $send_to = [];

        //$users_query = db_query("select id from app_entity_1 where field_6 in (" . $value . ") and field_5=1");
        $users_query = \K::model()->db_fetch('app_entity_1', [
            //TODO Refactoring to classic field_6 = ?
            'field_6 in ( ? ) and field_5 = 1',
            $value
        ], [], 'id');

        //while ($users = db_fetch_array($users_query)) {
        foreach ($users_query as $users) {
            $users = $users->cast();

            $send_to[] = $users['id'];
        }

        return $send_to;
    }
}