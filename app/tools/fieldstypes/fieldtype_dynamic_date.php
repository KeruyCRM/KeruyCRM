<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_dynamic_date
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_DYNAMIC_DATE_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_FORMULA . \Models\Main\Fields::get_available_fields_helper(
                    $_POST['entities_id'],
                    'fields_configuration_formula'
                ),
            'name' => 'formula',
            'type' => 'code_small',
            'tooltip_icon' => \K::$fw->TEXT_FORMULA_TIP_USAGE,
            'tooltip' => \K::$fw->TEXT_FORMULA_TIP,
            'params' => ['class' => 'form-control code', 'mode' => 'sql']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_DATE_FORMAT,
            'name' => 'date_format',
            'type' => 'input',
            'tooltip' => \K::$fw->TEXT_DEFAULT . ': ' . \K::$fw->CFG_APP_DATE_FORMAT . ', ' . \K::$fw->TEXT_DATE_FORMAT_INFO,
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $cfg[\K::$fw->TEXT_COLOR][] = [
            'title' => \K::$fw->TEXT_OVERDUE_DATES,
            'name' => 'background',
            'type' => 'colorpicker',
            'tooltip_icon' => \K::$fw->TEXT_DATE_BACKGROUND_TOOLTIP
        ];

        $cfg[\K::$fw->TEXT_COLOR][] = [
            'title' => \K::$fw->TEXT_DAYS_BEFORE_DATE,
            'name' => 'day_before_date',
            'type' => 'input-with-colorpicker',
            'tooltip_icon' => \K::$fw->TEXT_DAYS_BEFORE_DATE_TIP
        ];

        $cfg[\K::$fw->TEXT_COLOR][] = [
            'title' => \K::$fw->TEXT_DAYS_BEFORE_DATE . ' 2',
            'name' => 'day_before_date2',
            'type' => 'input-with-colorpicker',
            'tooltip_icon' => \K::$fw->TEXT_DAYS_BEFORE_DATE_TIP
        ];

        $choices = ['' => ''];

        /*$fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_stages','fieldtype_dropdown','fieldtype_radioboxes','fieldtype_dropdown_multiple','fieldtype_tags','fieldtype_checkboxes','fieldtype_autostatus') and entities_id='" . db_input(
                \K::$fw->POST['entities_id']
            ) . "'"
        );*/

        $typeIn = \K::model()->quoteToString(
            [
                'fieldtype_stages',
                'fieldtype_dropdown',
                'fieldtype_radioboxes',
                'fieldtype_dropdown_multiple',
                'fieldtype_tags',
                'fieldtype_checkboxes',
                'fieldtype_autostatus'
            ]
        );

        $fields_query = \K::model()->db_fetch('app_fields', [
            'type in (' . $typeIn . ') and entities_id = ?',
            \K::$fw->POST['entities_id']
        ], [], 'id,name');

        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $fields = $fields->cast();

            $choices[$fields['id']] = $fields['name'];
        }

        $cfg[\K::$fw->TEXT_COLOR][] = [
            'title' => \K::$fw->TEXT_DISABLE_COLOR,
            'name' => 'disable_color_by_field',
            'type' => 'dropdown',
            'choices' => $choices,
            'tooltip_icon' => \K::$fw->TEXT_DISABLE_COLOR_BY_FIELD_TIP,
            'params' => [
                'class' => 'form-control input-large',
                'onChange' => 'fields_types_ajax_configuration(\'disable_color_by_field_values\',this.value)'
            ],
        ];

        $cfg[\K::$fw->TEXT_COLOR][] = [
            'name' => 'disable_color_by_field_values',
            'type' => 'ajax',
            'html' => '<script>fields_types_ajax_configuration(\'disable_color_by_field_values\',$("#fields_configuration_disable_color_by_field").val())</script>'
        ];

        return $cfg;
    }

    public function get_ajax_configuration($name, $value)
    {
        $cfg = [];

        switch ($name) {
            case 'disable_color_by_field_values':
                if (strlen($value)) {
                    //$field_query = db_query("select id, name, configuration from app_fields where id='" . $value . "'");

                    $field = \K::model()->db_fetch_one('app_fields', [
                        'id = ?',
                        $value
                    ], [], 'id,name,configuration');

                    if ($field) {
                        $field_cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

                        if ($field_cfg->get('use_global_list') > 0) {
                            $choices = \Models\Main\Global_lists::get_choices(
                                $field_cfg->get('use_global_list'),
                                false
                            );
                        } else {
                            $choices = \Models\Main\Fields_choices::get_choices($field['id'], false);
                        }

                        $cfg[] = [
                            'title' => $field['name'],
                            'name' => 'disable_color_by_field_choices',
                            'type' => 'dropdown',
                            'choices' => $choices,
                            'params' => ['class' => 'form-control input-large chosen-select', 'multiple' => 'multiple'],
                        ];
                    }
                }
                break;
        }

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        return false;
    }

    public function process($options)
    {
        return $options['value'];
    }

    public function output($options)
    {
        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        if (isset($options['is_export']) and strlen($options['value']) > 0 and $options['value'] != 0) {
            return \Helpers\App::format_date($options['value'], $cfg->get('date_format'));
        } elseif (strlen($options['value']) > 0 and $options['value'] != 0) {
            $html = \Helpers\App::format_date($options['value'], $cfg->get('date_format'));

            //return simple value if color is disabled
            if (strlen($cfg->get('disable_color_by_field'))) {
                if (isset($options['item']['field_' . $cfg->get('disable_color_by_field')])) {
                    if (is_array($cfg->get('disable_color_by_field_choices'))) {
                        foreach ($cfg->get('disable_color_by_field_choices') as $choices_id) {
                            if (in_array(
                                $choices_id,
                                explode(',', $options['item']['field_' . $cfg->get('disable_color_by_field')])
                            )) {
                                return $html;
                            }
                        }
                    }
                }
            }

            //highlight field if overdue date    
            if ((date('Y-m-d', $options['value']) == date('Y-m-d') or $options['value'] < time()) and strlen(
                    $cfg->get('background')
                ) > 0) {
                $html = \Helpers\App::render_bg_color_block(
                    $cfg->get('background'),
                    \Helpers\App::format_date($options['value'], $cfg->get('date_format'))
                );
            }

            //highlight field before due date
            if (strlen($cfg->get('day_before_date')) > 0 and strlen(
                    $cfg->get('day_before_date_color')
                ) > 0 and $options['value'] > time()) {
                if ($options['value'] < strtotime('+' . $cfg->get('day_before_date') . ' day')) {
                    $html = \Helpers\App::render_bg_color_block(
                        $cfg->get('day_before_date_color'),
                        \Helpers\App::format_date($options['value'], $cfg->get('date_format'))
                    );
                }
            }

            //highlight 2 field before due date
            if (strlen($cfg->get('day_before_date2')) > 0 and strlen(
                    $cfg->get('day_before_date2_color')
                ) > 0 and $options['value'] > time()) {
                if ($options['value'] < strtotime('+' . $cfg->get('day_before_date2') . ' day')) {
                    $html = \Helpers\App::render_bg_color_block(
                        $cfg->get('day_before_date2_color'),
                        \Helpers\App::format_date($options['value'], $cfg->get('date_format'))
                    );
                }
            }

            //return single value      
            return $html;
        } else {
            return '';
        }
    }

    public function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $sql = \Models\Main\Reports\Reports::prepare_dates_sql_filters($filters, false);

        if (count($sql) > 0) {
            \K::$fw->sql_query_having[$options['entities_id']][] = implode(' and ', $sql);
        }

        return $sql_query;
    }

    //function to update item if there are any not dynamic query
    public static function update_items_fields($entities_id, $items_id, $item_info = false)
    {
        $update_fields = [];

        if (isset(\K::$fw->app_fields_cache[$entities_id])) {
            foreach (\K::$fw->app_fields_cache[$entities_id] as $fields) {
                if ($fields['type'] == 'fieldtype_dynamic_date') {
                    $update_fields[] = $fields['id'];
                }
            }
        }

        if (count($update_fields)) {
            if (!$item_info) {
                $item_info = \K::model()->db_query_exec_one(
                    'select e.* ' . \Tools\FieldsTypes\Fieldtype_formula::prepare_query_select(
                        $entities_id,
                        ''
                    ) . ' from app_entity_' . (int)$entities_id . ' e where e.id = ?',
                    $items_id
                );
                //$item_info = $item_info_query[0] ?? '';
            }

            $forceCommit = \K::model()->forceCommit();

            foreach ($update_fields as $fields_id) {
                /*db_query(
                    "update app_entity_{$entities_id} set field_{$fields_id}='" . $item_info['field_' . $fields_id] . "' where id='" . db_input(
                        $items_id
                    ) . "'"
                );*/

                \K::model()->db_update('app_entity_' . $entities_id, [
                    'field_' . (int)$fields_id => $item_info['field_' . $fields_id]
                ], ['id = ?', $items_id]);
            }

            if ($forceCommit) {
                \K::model()->commit();
            }
        }
    }

    public static function prepare_select_sql($field_info)
    {
        $cfg = new \Models\Main\Fields_types_cfg($field_info['configuration']);

        $formula = $cfg->get('formula');

        if (!strlen($formula)) {
            return 'e.field_' . (int)$field_info['id'];
        }

        $formulas_fields = [];

        foreach (\K::$fw->app_formula_fields_cache[$field_info['entities_id']] as $fields) {
            $cfg = \Models\Main\Fields_types::parse_configuration($fields['configuration']);

            if (strlen($cfg['formula'])) {
                $formulas_fields[$fields['id']] = '(' . $cfg['formula'] . ')';
            }
        }

        //prepare formula fields
        $formula = \Tools\FieldsTypes\Fieldtype_formula::prepare_formula_fields($formulas_fields, $formula);

        //handle get_value()
        $formula = \Tools\FieldsTypes\Fieldtype_formula::prepare_choices_get_value_function(
            $field_info['entities_id'],
            $formula
        );

        //prepare parent items values
        $formula = \Tools\FieldsTypes\Fieldtype_formula::prepare_parent_entity_item_value(
            $field_info['entities_id'],
            $formula
        );

        //prepare [TODAY]
        $formula = str_replace('[TODAY]', \Helpers\App::get_date_timestamp(date('Y-m-d')), $formula);

        $formula = str_replace('[id]', 'e.id', $formula);
        $formula = str_replace('[date_added]', 'e.date_added', $formula);
        $formula = str_replace('[created_by]', 'e.created_by', $formula);
        $formula = str_replace('[parent_item_id]', 'e.parent_item_id', $formula);
        $formula = str_replace('[current_user_id]', \K::$fw->app_user['id'], $formula);

        $available_fields = \K::$fw->app_not_formula_fields_cache[$field_info['entities_id']];
        foreach ($available_fields as $fields_id) {
            //handler mysql query field type in formula
            if (strstr(
                    $formula,
                    '[' . $fields_id . ']'
                ) and \K::$fw->app_fields_cache[$field_info['entities_id']][$fields_id]['type'] == 'fieldtype_mysql_query') {
                $formula = str_replace(
                    '[' . $fields_id . ']',
                    \Tools\FieldsTypes\Fieldtype_mysql_query::prepare_query(
                        \K::$fw->app_fields_cache[$field_info['entities_id']][$fields_id],
                        'e',
                        true
                    ),
                    $formula
                );
            } else {
                $formula = str_replace('[' . $fields_id . ']', 'e.field_' . (int)$fields_id, $formula);
            }
        }

        return '(' . $formula . ')';
    }
}