<?php

namespace Tools\FieldsTypes;

class Fieldtype_stages
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_STAGES_TITLE, 'has_choices' => true];
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

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HIDE_DROPDOWN,
            'tooltip_icon' => \K::$fw->TEXT_HIDDEN_FIELDS_IN_FORM,
            'name' => 'hide_in_form',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_STAGES_PANEL][] = [
            'title' => \K::$fw->TEXT_TYPE,
            'name' => 'panel_type',
            'type' => 'dropdown',
            'params' => ['class' => 'form-control input-medium'],
            'choices' => stages_panel::get_type_choices()
        ];

        $choises = [
            'all' => \K::$fw->TEXT_ALL_STAGES,
            'consistently' => \K::$fw->TEXT_CONSISTENTLY,
            'branching' => \K::$fw->TEXT_BRANCHING,
        ];

        $cfg[\K::$fw->TEXT_STAGES_PANEL][] = [
            'title' => \K::$fw->TEXT_SHOW,
            'name' => 'display_type',
            'type' => 'dropdown',
            'choices' => $choises,
            'params' => ['class' => 'form-control input-large'],
            'tooltip' => '
            <span form_display_rules="fields_configuration_display_type:consistently">' . \K::$fw->TEXT_FIELDTYPE_STAGES_SHOW_CONSISTENTLY_TIP . '</span>
            <span form_display_rules="fields_configuration_display_type:branching">' . \K::$fw->TEXT_FIELDTYPE_STAGES_SHOW_BRANCHING_TIP . '</span>'
        ];

        $cfg[\K::$fw->TEXT_STAGES_PANEL][] = [
            'title' => \K::$fw->TEXT_COLOR,
            'name' => 'color',
            'type' => 'colorpicker'
        ];

        $cfg[\K::$fw->TEXT_STAGES_PANEL][] = [
            'title' => \K::$fw->TEXT_ACTIVE_ITEM_COLOR,
            'name' => 'color_active',
            'type' => 'colorpicker'
        ];

        $cfg[\K::$fw->TEXT_STAGES_PANEL][] = [
            'title' => \K::$fw->TEXT_ACTION_BY_CLICK,
            'name' => 'click_action',
            'type' => 'dropdown',
            'params' => ['class' => 'form-control input-xlarge'],
            'choices' => [
                '' => '',
                'change_value' => \K::$fw->TEXT_ALLOW_CHANGING_VALUE,
                'change_value_next_step' => \K::$fw->TEXT_ALLOW_CHANGING_VALUE_NEXT_STEP
            ]
        ];

        $cfg[\K::$fw->TEXT_STAGES_PANEL][] = [
            'title' => \K::$fw->TEXT_ADD_COMMENT,
            'name' => 'add_comment',
            'type' => 'checkbox'
        ];

        //confirmation text

        $field = db_find('app_fields', _post::int('id'));
        $field_cfg = new \Tools\Fields_types_cfg($field['configuration']);

        if ($field_cfg->get('use_global_list') > 0) {
            $choices = global_lists::get_choices($field_cfg->get('use_global_list'), false);
        } else {
            $choices = fields_choices::get_choices($field['id'], false);
        }

        foreach ($choices as $choice_id => $choice_name) {
            $cfg[\K::$fw->TEXT_CONFIRMATION_TEXT][] = [
                'title' => $choice_name,
                'name' => 'confirmation_text_for_choice_' . $choice_id,
                'type' => 'textarea',
                'params' => ['class' => 'form-control input-xlarge textarea-small']
            ];
        }

        $cfg[\K::$fw->TEXT_ACTION][] = [
            'html' => '<p>' . \K::$fw->TEXT_FIELDTYPE_STAGES_ACTION_TIP . '</p>',
            'type' => 'html'
        ];

        if (\Helpers\App::is_ext_installed()) {
            $processes_chocies = [];
            $processes_chocies[0] = '';
            $processes_query = db_query(
                "select id, name from app_ext_processes where entities_id='" . _post::int(
                    'entities_id'
                ) . "' order by sort_order, name"
            );
            while ($processes = db_fetch_array($processes_query)) {
                $processes_chocies[$processes['id']] = $processes['name'];
            }

            foreach ($choices as $choice_id => $choice_name) {
                $cfg[\K::$fw->TEXT_ACTION][] = [
                    'title' => $choice_name,
                    'name' => 'run_process_for_choice_' . $choice_id,
                    'type' => 'dropdown',
                    'choices' => $processes_chocies,
                    'params' => ['class' => 'form-control input-large']
                ];
            }
        } else {
            $cfg[\K::$fw->TEXT_ACTION][] = [
                'html' => '<div class="alert alert-warning">' . \K::$fw->TEXT_EXTENSION_REQUIRED . '</div>',
                'type' => 'html'
            ];
        }

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        global $app_module_path;

        $cfg = new \Tools\Fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'form-control ' . $cfg->get(
                    'width'
                ) . ' field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')
        ];

        //use global lists if exsit
        if ($cfg->get('use_global_list') > 0) {
            $choices = global_lists::get_choices(
                $cfg->get('use_global_list'),
                (($field['is_required'] == 0 or strlen($cfg->get('default_text')) > 0) ? true : false),
                $cfg->get('default_text'),
                $obj['field_' . $field['id']],
                true
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

        if (($cfg->get('click_action') == 'change_value_next_step' or $cfg->get(
                    'hide_in_form'
                ) == 1) and $app_module_path == 'items/form') {
            return input_hidden_tag(
                    'fields[' . $field['id'] . ']',
                    $value
                ) . (isset($choices[$value]) ? '<p class="form-control-static">' . $choices[$value] . '</p>' : '');
        } else {
            return select_tag('fields[' . $field['id'] . ']', $choices, $value, $attributes);
        }
    }

    public function process($options)
    {
        global $app_changed_fields, $app_choices_cache, $app_global_choices_cache;

        if (!$options['is_new_item']) {
            $cfg = new \Tools\Fields_types_cfg($options['field']['configuration']);

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
        $cfg = new \Tools\Fields_types_cfg($options['field']['configuration']);

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