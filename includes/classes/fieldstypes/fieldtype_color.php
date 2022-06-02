<?php

class fieldtype_color
{

    public $options;

    function __construct()
    {
        $this->options = ['title' => TEXT_FIELDTYPE_COLOR_TITLE, 'has_choices' => true];
    }

    function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => TEXT_DISPLAY_USERS_AS,
            'name' => 'display_as',
            'type' => 'dropdown',
            'choices' => [
                'dropdown' => TEXT_DISPLAY_USERS_AS_DROPDOWN,
                'dropdown_muliple' => TEXT_DISPLAY_USERS_AS_DROPDOWN_MULTIPLE,
                'checkboxes' => TEXT_DISPLAY_USERS_AS_CHECKBOXES,
            ],
            'params' => ['class' => 'form-control input-large']
        ];

        $cfg[] = [
            'title' => TEXT_WIDHT,
            'name' => 'width',
            'type' => 'dropdown',
            'choices' => [
                'input-small' => TEXT_INPTUT_SMALL,
                'input-medium' => TEXT_INPUT_MEDIUM,
                'input-large' => TEXT_INPUT_LARGE,
                'input-xlarge' => TEXT_INPUT_XLARGE
            ],
            'tooltip' => TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium'],
            'form_group' => ['form_display_rules' => 'fields_configuration_display_as:dropdown,dropdown_muliple']
        ];

        $cfg[] = [
            'title' => TEXT_COLUMN,
            'name' => 'ul-class',
            'type' => 'dropdown',
            'choices' => fieldtype_radioboxes::get_display_as_choices(),
            'default' => 'list-column-1',
            'params' => ['class' => 'form-control input-medium'],
            'form_group' => ['form_display_rules' => 'fields_configuration_display_as:checkboxes']
        ];

        $cfg[] = [
            'title' => TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $cfg[] = [
            'title' => TEXT_DISPLAY_CHOICES_VALUES,
            'name' => 'display_choices_values',
            'type' => 'checkbox',
            'tooltip_icon' => TEXT_DISPLAY_CHOICES_VALUES_TIP
        ];

        //cfg global list if exist
        if (count($choices = global_lists::get_lists_choices()) > 0) {
            $cfg[] = [
                'title' => TEXT_USE_GLOBAL_LIST,
                'name' => 'use_global_list',
                'type' => 'dropdown',
                'choices' => $choices,
                'tooltip' => TEXT_USE_GLOBAL_LIST_TOOLTIP,
                'params' => ['class' => 'form-control input-medium']
            ];
        }

        return $cfg;
    }

    function render($field, $obj, $params = [])
    {
        $cfg = new fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'form-control ' . $cfg->get(
                    'width'
                ) . ' chosen-select field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : ''),
            'data-placeholder' => TEXT_SELECT_SOME_VALUES
        ];

        //use global lists if exsit    
        if ($cfg->get('use_global_list') > 0) {
            $choices = global_lists::get_choices_with_color(
                $cfg->get('use_global_list'),
                ($field['is_required'] == 1 ? false : true),
                '',
                $obj['field_' . $field['id']],
                true
            );
            $default_id = global_lists::get_choices_default_id($cfg->get('use_global_list'));
        } else {
            $choices = fields_choices::get_choices_with_color(
                $field['id'],
                ($field['is_required'] == 1 ? false : true),
                '',
                $cfg->get('display_choices_values'),
                $obj['field_' . $field['id']],
                true
            );
            $default_id = fields_choices::get_default_id($field['id']);
        }

        $value = ($obj['field_' . $field['id']] > 0 ? $obj['field_' . $field['id']] : ($params['form'] == 'comment' ? '' : $default_id));

        $html = '';
        switch ($cfg->get('display_as')) {
            case 'dropdown':
                $html = select_tag_with_color('fields[' . $field['id'] . ']', $choices, $value, $attributes);
                break;
            case 'dropdown_muliple':
                $attributes['multiple'] = 'multiple';
                $html = select_tag_with_color('fields[' . $field['id'] . '][]', $choices, $value, $attributes);
                break;
            case 'checkboxes':
                $attributes = ['class' => 'field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')];
                $attributes['ul-class'] = $cfg->get('ul-class');

                if (isset($choices[''])) {
                    unset($choices['']);
                }

                $html = '<div class="checkbox-list ' . ($attributes['ul-class'] == 'list-inline' ? ' form-control-static' : '') . '">' . select_checkboxes_ul_color_tag(
                        'fields[' . $field['id'] . ']',
                        $choices,
                        $value,
                        $attributes
                    ) . '</div>';

                break;
        }

        return $html . fields_types::custom_error_handler($field['id']);
    }

    function process($options)
    {
        return (is_array($options['value']) ? implode(',', $options['value']) : $options['value']);
    }

    function output($options)
    {
        $is_export = (isset($options['is_export']) and $options['is_export'] == true) ? true : false;

        $cfg = new fields_types_cfg($options['field']['configuration']);

        //render global list value
        if ($cfg->get('use_global_list') > 0) {
            return global_lists::render_value($options['value'], $is_export);
        } else {
            return fields_choices::render_value($options['value'], $is_export);
        }
    }

    function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $prefix = (strlen($options['prefix']) ? $options['prefix'] : 'e');

        if (strlen($filters['filters_values']) > 0) {
            $sql_query[] = "(select count(*) from app_entity_" . $options['entities_id'] . "_values as cv where cv.items_id=" . $prefix . ".id and cv.fields_id='" . db_input(
                    $options['filters']['fields_id']
                ) . "' and cv.value in (" . $filters['filters_values'] . ")) " . ($filters['filters_condition'] == 'include' ? '>0' : '=0');
        }

        return $sql_query;
    }

}
