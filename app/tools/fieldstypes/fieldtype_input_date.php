<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_input_date
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_INPUT_DATE_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_NOTIFY_WHEN_CHANGED,
            'name' => 'notify_when_changed',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_NOTIFY_WHEN_CHANGED_TIP
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_DATE_FORMAT,
            'name' => 'date_format',
            'type' => 'input',
            'tooltip' => \K::$fw->TEXT_DEFAULT . ': ' . \K::$fw->CFG_APP_DATE_FORMAT . ', ' . \K::$fw->TEXT_DATE_FORMAT_INFO,
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[\K::$fw->TEXT_EXTRA][] = [
            'title' => \K::$fw->TEXT_DEFAULT_DATE,
            'name' => 'default_value',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_DEFAULT_DATE_INFO,
            'params' => ['class' => 'form-control input-small', 'type' => 'number']
        ];

        $cfg[\K::$fw->TEXT_EXTRA][] = [
            'title' => \K::$fw->TEXT_MIN_DATE,
            'name' => 'min_date',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_DEFAULT_DATE_INFO,
            'params' => ['class' => 'form-control input-small', 'type' => 'number']
        ];

        $cfg[\K::$fw->TEXT_EXTRA][] = [
            'title' => \K::$fw->TEXT_MAX_DATE,
            'name' => 'max_date',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_DEFAULT_DATE_INFO,
            'params' => ['class' => 'form-control input-small', 'type' => 'number']
        ];

        $cfg[\K::$fw->TEXT_EXTRA][] = [
            'title' => \K::$fw->TEXT_IS_UNIQUE_FIELD_VALUE,
            'name' => 'is_unique',
            'type' => 'dropdown',
            'choices' => \Models\Main\Fields_types::get_is_unique_choices(\K::$fw->POST['entities_id']),
            'tooltip_icon' => \K::$fw->TEXT_IS_UNIQUE_FIELD_VALUE_TIP,
            'params' => ['class' => 'form-control input-large']
        ];

        $cfg[\K::$fw->TEXT_EXTRA][] = [
            'title' => \K::$fw->TEXT_ERROR_MESSAGE,
            'name' => 'unique_error_msg',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_UNIQUE_FIELD_VALUE_ERROR_MSG_TIP,
            'tooltip' => \K::$fw->TEXT_DEFAULT . ': ' . \K::$fw->TEXT_UNIQUE_FIELD_VALUE_ERROR,
            'params' => ['class' => 'form-control input-xlarge']
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

        /*$fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_stages','fieldtype_dropdown','fieldtype_radioboxes','fieldtype_dropdown_multiple','fieldtype_tags','fieldtype_checkboxes','fieldtype_autostatus') and entities_id='" . db_input(
                $_POST['entities_id']
            ) . "'"
        );*/

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
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        if (strlen($obj['field_' . $field['id']]) > 0 and $obj['field_' . $field['id']] != 0) {
            $value = date('Y-m-d', $obj['field_' . $field['id']]);
        } else {
            $value = '';
        }

        if (!isset($params['is_new_item'])) {
            $params['is_new_item'] = false;
        }

        //handle default value
        if ($params['is_new_item'] == true and strlen($cfg->get('default_value')) > 0 and (strlen(
                    $obj['field_' . $field['id']]
                ) == 0 or $obj['field_' . $field['id']] == 0)) {
            $value = date('Y-m-d', strtotime("+" . (int)$cfg->get('default_value') . " day"));
        }

        $attributes = [
            'class' => 'form-control fieldtype_input_date field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '') . ($cfg->get(
                    'is_unique'
                ) > 0 ? ' is-unique' : '')
        ];

        $attributes = \Models\Main\Fields_types::prepare_uniquer_error_msg_param($attributes, $cfg);

        //handle extra attributes
        $extra_attributes = [];

        if (strlen($cfg->get('min_date'))) {
            $extra_attributes[] = 'data-date-start-date="' . date(
                    'Y-m-d',
                    strtotime("+" . (int)$cfg->get('min_date') . " day")
                ) . '"';
        }

        if (strlen($cfg->get('max_date'))) {
            $extra_attributes[] = 'data-date-end-date="' . date(
                    'Y-m-d',
                    strtotime("+" . (int)$cfg->get('max_date') . " day")
                ) . '"';
        }

        if (strlen($cfg->get('min_date')) or strlen($cfg->get('max_date'))) {
            $attributes['readonly'] = 'readonly';
        }

        return '<div class="input-group input-medium date datepicker" ' . implode(
                ' ',
                $extra_attributes
            ) . '>' . \Helpers\Html::input_tag(
                'fields[' . $field['id'] . ']',
                $value,
                $attributes
            ) . '<span class="input-group-btn"><button class="btn btn-default date-set" type="button"><i class="fa fa-calendar"></i></button></span></div>';
    }

    public function process($options)
    {
        $value = !is_numeric($options['value']) ? (int)\Helpers\App::get_date_timestamp(
            $options['value']
        ) : (int)$options['value'];

        if (!$options['is_new_item']) {
            $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

            if ($value != $options['current_field_value'] and $cfg->get('notify_when_changed') == 1) {
                \K::$fw->app_changed_fields[] = [
                    'name' => $options['field']['name'],
                    'value' => \Helpers\App::format_date($value),
                    'fields_id' => $options['field']['id'],
                    'fields_value' => $value,
                ];
            }
        }

        return $value;
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

        $sql = \Models\Main\Reports\Reports::prepare_dates_sql_filters($filters, $options['prefix']);

        if (count($sql) > 0) {
            $sql_query[] = implode(' and ', $sql);
        }

        return $sql_query;
    }
}