<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_color
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_COLOR_TITLE, 'has_choices' => true];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DISPLAY_USERS_AS,
            'name' => 'display_as',
            'type' => 'dropdown',
            'choices' => [
                'dropdown' => \K::$fw->TEXT_DISPLAY_USERS_AS_DROPDOWN,

                'dropdown_multiple' => \K::$fw->TEXT_DISPLAY_USERS_AS_DROPDOWN_MULTIPLE,
                'checkboxes' => \K::$fw->TEXT_DISPLAY_USERS_AS_CHECKBOXES,
            ],
            'params' => ['class' => 'form-control input-large']
        ];

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
            'tooltip' => \K::$fw->TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium'],
            'form_group' => ['form_display_rules' => 'fields_configuration_display_as:dropdown,dropdown_multiple']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_COLUMN,
            'name' => 'ul-class',
            'type' => 'dropdown',
            'choices' => \Tools\FieldsTypes\Fieldtype_radioboxes::get_display_as_choices(),
            'default' => 'list-column-1',
            'params' => ['class' => 'form-control input-medium'],
            'form_group' => ['form_display_rules' => 'fields_configuration_display_as:checkboxes']
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
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'form-control ' . $cfg->get(
                    'width'
                ) . ' chosen-select field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : ''),
            'data-placeholder' => \K::$fw->TEXT_SELECT_SOME_VALUES
        ];

        //use global lists if exsit    
        if ($cfg->get('use_global_list') > 0) {
            $choices = \Models\Main\Global_lists::get_choices_with_color(
                $cfg->get('use_global_list'),
                ($field['is_required'] == 1 ? false : true),
                '',
                $obj['field_' . $field['id']],
                true
            );
            $default_id = \Models\Main\Global_lists::get_choices_default_id($cfg->get('use_global_list'));
        } else {
            $choices = \Models\Main\Fields_choices::get_choices_with_color(
                $field['id'],
                ($field['is_required'] == 1 ? false : true),
                '',
                $cfg->get('display_choices_values'),
                $obj['field_' . $field['id']],
                true
            );
            $default_id = \Models\Main\Fields_choices::get_default_id($field['id']);
        }

        $value = ($obj['field_' . $field['id']] > 0 ? $obj['field_' . $field['id']] : ($params['form'] == 'comment' ? '' : $default_id));

        $html = '';
        switch ($cfg->get('display_as')) {
            case 'dropdown':
                $html = \Helpers\Html::select_tag_with_color(
                    'fields[' . $field['id'] . ']',
                    $choices,
                    $value,
                    $attributes
                );
                break;
            case 'dropdown_multiple':
                $attributes['multiple'] = 'multiple';
                $html = \Helpers\Html::select_tag_with_color(
                    'fields[' . $field['id'] . '][]',
                    $choices,
                    $value,
                    $attributes
                );
                break;
            case 'checkboxes':
                $attributes = ['class' => 'field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')];
                $attributes['ul-class'] = $cfg->get('ul-class');

                if (isset($choices[''])) {
                    unset($choices['']);
                }

                $html = '<div class="checkbox-list ' . ($attributes['ul-class'] == 'list-inline' ? ' form-control-static' : '') . '">' . \Helpers\Html::select_checkboxes_ul_color_tag(
                        'fields[' . $field['id'] . ']',
                        $choices,
                        $value,
                        $attributes
                    ) . '</div>';

                break;
        }

        return $html . \Models\Main\Fields_types::custom_error_handler($field['id']);
    }

    public function process($options)
    {
        return (is_array($options['value']) ? implode(',', $options['value']) : $options['value']);
    }

    public function output($options)
    {
        $is_export = (isset($options['is_export']) and $options['is_export'] == true) ? true : false;

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