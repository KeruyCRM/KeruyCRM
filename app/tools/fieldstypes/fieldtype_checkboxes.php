<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_checkboxes
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_CHECKBOXES_TITLE, 'has_choices' => true];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DISPLAY_AS,
            'name' => 'display_as',
            'type' => 'dropdown',
            'choices' => fieldtype_radioboxes::get_display_as_choices(),
            'default' => 'list-column-1',
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DISPLAY_CHOICES_VALUES,
            'name' => 'display_choices_values',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_DISPLAY_CHOICES_VALUES_TIP
        ];

        //cfg global list if exist
        if (count($choices = \Models\Main\Global_lists::get_lists_choices()) > 0) {
            $cfg[] = [
                'title' => \K::$fw->TEXT_USE_GLOBAL_LIST,
                'name' => 'use_global_list',
                'type' => 'dropdown',
                'choices' => $choices,
                'tooltip' => \K::$fw->TEXT_USE_GLOBAL_LIST_TOOLTIP,
                'params' => ['class' => 'form-control input-medium']
            ];
        }

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $attributes = ['class' => 'field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')];

        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        //use global lists if exsit
        if ($cfg->get('use_global_list') > 0) {
            $choices = \Models\Main\Global_lists::get_choices(
                $cfg->get('use_global_list'),
                false,
                '',
                $obj['field_' . $field['id']],
                true
            );
            $default_id = \Models\Main\Global_lists::get_choices_default_id($cfg->get('use_global_list'));
        } else {
            $choices = \Models\Main\Fields_choices::get_choices(
                $field['id'],
                false,
                '',
                $cfg->get('display_choices_values'),
                $obj['field_' . $field['id']],
                true
            );
            $default_id = \Models\Main\Fields_choices::get_default_id($field['id']);
        }

        //reset default id for new item
        if (isset($params['is_new_item']) and $params['is_new_item'] != 1) {
            $default_id = '';
        }

        $value = ($obj['field_' . $field['id']] > 0 ? $obj['field_' . $field['id']] : $default_id);

        if ($cfg->get('display_as') == '' or $cfg->get('display_as') == 'list-column-1') {
            return '
                    <div class="checkbox-list ' . (count(
                    $choices
                ) == 1 ? ' checkbox-list-singe' : '') . ($field['is_required'] == 1 ? ' required' : '') . '">' .
                \Helpers\Html::select_checkboxes_tag('fields[' . $field['id'] . ']', $choices, $value, $attributes) .
                '</div>';
        } else {
            $attributes['ul-class'] = $cfg->get('display_as');
            return '<div class="checkbox-list ' . ($attributes['ul-class'] == 'list-inline' ? ' form-control-static' : '') . '">' . \Helpers\Html::select_checkboxes_ul_tag(
                    'fields[' . $field['id'] . ']',
                    $choices,
                    $value,
                    $attributes
                ) . '</div>';
        }
    }

    public function process($options)
    {
        return (is_array($options['value']) ? implode(',', $options['value']) : $options['value']);
    }

    public function output($options)
    {
        $is_export = isset($options['is_export']);

        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        //render global list value
        if ($cfg->get('use_global_list') > 0) {
            return \Models\Main\Global_lists::render_value($options['value'], $is_export);
        } else {
            return \Models\Main\Fields_choices::render_value($options['value'], $is_export);
        }
    }

    public function reports_query($options)
    {
        return \Models\Main\Reports\Reports::getReportsQueryValues($options);
    }
}