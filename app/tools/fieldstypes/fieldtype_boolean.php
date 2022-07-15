<?php

namespace Tools\FieldsTypes;

class Fieldtype_boolean
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_BOOLEAN_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_NOTIFY_WHEN_CHANGED,
            'name' => 'notify_when_changed',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_NOTIFY_WHEN_CHANGED_TIP
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DEFAULT_TEXT,
            'name' => 'default_text',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_DEFAULT_TEXT_INFO,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DEFAULT_VALUE,
            'name' => 'default_value',
            'type' => 'dropdown',
            'choices' => ['' => '', 'true' => \K::$fw->TEXT_BOOLEAN_TRUE, 'false' => \K::$fw->TEXT_BOOLEAN_FALSE],
            'params' => ['class' => 'form-control input-small']
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
            'tooltip_icon' => \K::$fw->TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_BOOLEAN_TRUE_VALUE,
            'name' => 'text_boolean_true',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_BOOLEAN_TRUE_VALUE_INFO,
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_BOOLEAN_FALSE_VALUE,
            'name' => 'text_boolean_false',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_BOOLEAN_FALSE_VALUE_INFO,
            'params' => ['class' => 'form-control input-small']
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'form-control ' . $cfg->get(
                    'width'
                ) . ' field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')
        ];

        $add_empty = (($field['is_required'] == 0 or strlen($cfg->get('default_text')) > 0) ? true : false);

        $choices = self::get_choices($field, $add_empty);

        $default_id = (!$add_empty ? 'true' : '');

        if (strlen($cfg->get('default_value'))) {
            $default_id = $cfg->get('default_value');
        }

        $value = (strlen($obj['field_' . $field['id']]) > 0 ? $obj['field_' . $field['id']] : $default_id);

        return select_tag('fields[' . $field['id'] . ']', $choices, $value, $attributes);
    }

    public static function get_choices($field, $add_empty = false)
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $choices = [];

        if ($add_empty) {
            $choices[''] = $cfg->get('default_text');
        }

        $choices['true'] = (strlen($cfg->get('text_boolean_true')) > 0 ? $cfg->get(
            'text_boolean_true'
        ) : \K::$fw->TEXT_BOOLEAN_TRUE);
        $choices['false'] = (strlen($cfg->get('text_boolean_true')) > 0 ? $cfg->get(
            'text_boolean_false'
        ) : \K::$fw->TEXT_BOOLEAN_FALSE);

        return $choices;
    }

    public static function get_boolean_value($field, $value)
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        switch ($value) {
            case 'true':
                return (strlen($cfg->get('text_boolean_true')) > 0 ? $cfg->get(
                    'text_boolean_true'
                ) : \K::$fw->TEXT_BOOLEAN_TRUE);
                break;
            case 'false':
                return (strlen($cfg->get('text_boolean_true')) > 0 ? $cfg->get(
                    'text_boolean_false'
                ) : \K::$fw->TEXT_BOOLEAN_FALSE);
                break;
            default:
                return '';
                break;
        }
    }

    public function process($options)
    {
        global $app_changed_fields, $app_choices_cache;

        if (!$options['is_new_item']) {
            $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

            if ($options['value'] != $options['current_field_value'] and $cfg->get('notify_when_changed') == 1) {
                $app_changed_fields[] = [
                    'name' => $options['field']['name'],
                    'value' => self::get_boolean_value($options['field'], $options['value']),
                    'fields_id' => $options['field']['id'],
                    'fields_value' => $options['value'],
                ];
            }
        }

        return $options['value'];
    }

    public function output($options)
    {
        return self::get_boolean_value($options['field'], $options['value']);
    }

    public function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $prefix = (strlen($options['prefix']) ? $options['prefix'] : 'e');

        $sql_query[] = $prefix . '.field_' . $filters['fields_id'] . ($filters['filters_condition'] == 'include' ? ' = ' : ' != ') . "'" . $filters['filters_values'] . "'";

        return $sql_query;
    }
}