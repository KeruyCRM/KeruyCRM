<?php

namespace Tools\FieldsTypes;

class Fieldtype_radioboxes
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::f3()->TEXT_FIELDTYPE_RADIOBOXES_TITLE, 'has_choices' => true];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::f3()->TEXT_DISPLAY_AS,
            'name' => 'display_as',
            'type' => 'dropdown',
            'choices' => self::get_display_as_choices(),
            'default' => 'list-column-1',
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[] = [
            'title' => \K::f3()->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::f3()->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $cfg[] = [
            'title' => \K::f3()->TEXT_DISPLAY_CHOICES_VALUES,
            'name' => 'display_choices_values',
            'type' => 'checkbox',
            'tooltip_icon' => \K::f3()->TEXT_DISPLAY_CHOICES_VALUES_TIP
        ];

        //cfg global list if exist
        if (count($choices = global_lists::get_lists_choices()) > 0) {
            $cfg[] = [
                'title' => \K::f3()->TEXT_USE_GLOBAL_LIST,
                'name' => 'use_global_list',
                'type' => 'dropdown',
                'choices' => $choices,
                'tooltip' => \K::f3()->TEXT_USE_GLOBAL_LIST_TOOLTIP,
                'params' => ['class' => 'form-control input-medium']
            ];
        }

        return $cfg;
    }

    public static function get_display_as_choices()
    {
        $choices = [
            'list-inline' => \K::f3()->TEXT_INLINE_LIST,
            'list-column-1' => \K::f3()->TEXT_COLUMN . ' 1',
            'list-column-2' => \K::f3()->TEXT_COLUMN . ' 2',
            'list-column-3' => \K::f3()->TEXT_COLUMN . ' 3',
            'list-column-4' => \K::f3()->TEXT_COLUMN . ' 4',
        ];

        return $choices;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : ''),
            'data-raido-list' => $field['id']
        ];

        //use global lists if exsit
        if ($cfg->get('use_global_list') > 0) {
            $choices = global_lists::get_choices(
                $cfg->get('use_global_list'),
                false,
                '',
                $obj['field_' . $field['id']],
                true
            );
            $default_id = global_lists::get_choices_default_id($cfg->get('use_global_list'));
        } else {
            $choices = fields_choices::get_choices(
                $field['id'],
                false,
                '',
                $cfg->get('display_choices_values'),
                $obj['field_' . $field['id']],
                true
            );
            $default_id = fields_choices::get_default_id($field['id']);
        }

        $value = ($obj['field_' . $field['id']] > 0 ? $obj['field_' . $field['id']] : $default_id);

        if ($cfg->get('display_as') == '' or $cfg->get('display_as') == 'list-column-1') {
            return '<div class="radio-list radio-list-' . $field['id'] . '">' . select_radioboxes_tag(
                    'fields[' . $field['id'] . ']',
                    $choices,
                    $value,
                    $attributes
                ) . '</div>';
        } else {
            $attributes['ul-class'] = $cfg->get('display_as');
            return '<div class="radio-list radio-list-' . $field['id'] . ($attributes['ul-class'] == 'list-inline' ? ' form-control-static' : '') . '">' . select_radioboxes_ul_tag(
                    'fields[' . $field['id'] . ']',
                    $choices,
                    $value,
                    $attributes
                ) . '</div>';
        }
    }

    public function process($options)
    {
        return $options['value'];
    }

    public function output($options)
    {
        $cfg = new fields_types_cfg($options['field']['configuration']);

        //render global list value
        if ($cfg->get('use_global_list') > 0) {
            return global_lists::render_value($options['value']);
        } else {
            return fields_choices::render_value($options['value']);
        }
    }

    public function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $prefix = (strlen($options['prefix']) ? $options['prefix'] : 'e');

        $sql_query[] = $prefix . '.field_' . $filters['fields_id'] . ($filters['filters_condition'] == 'include' ? ' in ' : ' not in ') . '(' . $filters['filters_values'] . ') ';

        return $sql_query;
    }
}