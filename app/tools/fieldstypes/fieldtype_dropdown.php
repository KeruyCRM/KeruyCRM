<?php

namespace Tools\FieldsTypes;

class Fieldtype_dropdown
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_DROPDOWN_TITLE, 'has_choices' => true];
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
            'title' => \K::$fw->TEXT_DEFAULT_TEXT,
            'name' => 'default_text',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_DEFAULT_TEXT_INFO,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
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

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_USE_SEARCH,
            'name' => 'use_search',
            'type' => 'dropdown',
            'choices' => ['0' => \K::$fw->TEXT_NO, '1' => \K::$fw->TEXT_YES],
            'tooltip' => \K::$fw->TEXT_USE_SEARCH_INFO,
            'params' => ['class' => 'form-control input-medium']
        ];

        //cfg global list if exist
        if (count($choices = global_lists::get_lists_choices()) > 0) {
            $cfg[\K::$fw->TEXT_SETTINGS][] = [
                'title' => \K::$fw->TEXT_USE_GLOBAL_LIST,
                'name' => 'use_global_list',
                'type' => 'dropdown',
                'choices' => $choices,
                'tooltip' => \K::$fw->TEXT_USE_GLOBAL_LIST_TOOLTIP,
                'params' => ['class' => 'form-control input-medium']
            ];
        }

        $cfg[\K::$fw->TEXT_VALUE][] = [
            'title' => \K::$fw->TEXT_DISPLAY_CHOICES_VALUES,
            'name' => 'display_choices_values',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_DISPLAY_CHOICES_VALUES_TIP
        ];

        $cfg[\K::$fw->TEXT_VALUE][] = [
            'title' => \K::$fw->TEXT_DISPLAY_PARENT_NAME,
            'name' => 'display_parent_name',
            'type' => 'dropdown',
            'choices' => [0 => \K::$fw->TEXT_NO, 1 => \K::$fw->TEXT_YES],
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[\K::$fw->TEXT_VALUE][] = [
            'title' => \K::$fw->TEXT_SEPARATOR,
            'name' => 'parent_name_separator',
            'type' => 'input',
            'default' => ':',
            'params' => ['class' => 'form-control input-medium'],
            'form_group' => ['form_display_rules' => 'fields_configuration_display_parent_name:1']
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'form-control ' . $cfg->get(
                    'width'
                ) . ' field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '') . ($cfg->get(
                    'use_search'
                ) == 1 ? ' chosen-select' : ''),
            'data-placeholder' => \K::$fw->TEXT_SELECT_SOME_VALUES
        ];

//use global lists if exsit    
        if ($cfg->get('use_global_list') > 0) {
            $choices = global_lists::get_choices(
                $cfg->get('use_global_list'),
                (($field['is_required'] == 0 or strlen($cfg->get('default_text')) > 0) ? true : false),
                $cfg->get('default_text'),
                $obj['field_' . $field['id']],
                true,
                $cfg->get('display_choices_values')
            );
            $default_id = global_lists::get_choices_default_id($cfg->get('use_global_list'));
        } else {
            $choices = fields_choices::get_choices(
                $field['id'],
                (($field['is_required'] == 0 or strlen($cfg->get('default_text')) > 0) ? true : false),
                $cfg->get('default_text'),
                $cfg->get('display_choices_values'),
                $obj['field_' . $field['id']],
                true
            );
            $default_id = fields_choices::get_default_id($field['id']);
        }

        $value = ($obj['field_' . $field['id']] > 0 ? $obj['field_' . $field['id']] : ($params['form'] == 'comment' ? '' : $default_id));

        return select_tag(
                'fields[' . $field['id'] . ']',
                $choices,
                $value,
                $attributes
            ) . fields_types::custom_error_handler($field['id']);
    }

    public function process($options)
    {
        global $app_changed_fields, $app_choices_cache, $app_global_choices_cache;

        if (!$options['is_new_item']) {
            $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

            if ($options['value'] > 0 and $options['value'] != $options['current_field_value'] and $cfg->get(
                    'notify_when_changed'
                ) == 1) {
                $app_changed_fields[] = [
                    'name' => $options['field']['name'],
                    'value' => ($cfg->get(
                        'use_global_list'
                    ) > 0 ? $app_global_choices_cache[$options['value']]['name'] : $app_choices_cache[$options['value']]['name']),
                    'fields_id' => $options['field']['id'],
                    'fields_value' => $options['value'],
                ];
            }
        }

        return $options['value'];
    }

    public function output($options)
    {
        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        if ($cfg->get('display_parent_name') == 1) {
            //render global list value
            if ($cfg->get('use_global_list') > 0) {
                return global_lists::render_value_with_parents(
                    $options['value'],
                    false,
                    $cfg->get('parent_name_separator')
                );
            } else {
                return fields_choices::render_value_with_parents(
                    $options['value'],
                    false,
                    $cfg->get('parent_name_separator')
                );
            }
        } else {
            //render global list value
            if ($cfg->get('use_global_list') > 0) {
                return global_lists::render_value($options['value']);
            } else {
                return fields_choices::render_value($options['value']);
            }
        }
    }

    public function reports_query($options)
    {
        return \Models\Main\Reports\Reports::getReportsQuery($options);
    }
}