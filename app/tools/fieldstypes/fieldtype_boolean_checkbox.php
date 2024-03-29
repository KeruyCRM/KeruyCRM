<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_boolean_checkbox
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_BOOLEAN_CHECKBOX_TITLE];
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
            'title' => \K::$fw->TEXT_DEFAULT_VALUE,
            'name' => 'default_value',
            'type' => 'dropdown',
            'choices' => ['' => '', 'true' => \K::$fw->TEXT_BOOLEAN_TRUE, 'false' => \K::$fw->TEXT_BOOLEAN_FALSE],
            'params' => ['class' => 'form-control input-small']
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

        $attributes = ['class' => 'single-checkbox field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')];

        if (!strlen($obj['field_' . $field['id']])) {
            $obj['field_' . $field['id']] = $cfg->get('default_value');
        }

        if ($obj['field_' . $field['id']] == 'true' or $obj['field_' . $field['id']] == 1) {
            $attributes['checked'] = 'checked';
        }

        return '<div class="form-control-static"><div class="checkbox-list single-checkbox-fields_' . $field['id'] . '">' . \Helpers\Html::input_checkbox_tag(
                'fields[' . $field['id'] . ']',
                1,
                $attributes
            ) . '</div></div>';
    }

    public static function get_boolean_value($field, $value)
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        switch ($value) {
            case '1':
            case 'true':
                return (strlen($cfg->get('text_boolean_true')) > 0 ? $cfg->get(
                    'text_boolean_true'
                ) : \K::$fw->TEXT_BOOLEAN_TRUE);
                break;
            case '':
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
        $options['value'] = (($options['value'] == 1 or $options['value'] == 'true') ? 'true' : 'false');

        if (!$options['is_new_item']) {
            $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

            if ($options['value'] != $options['current_field_value'] and $cfg->get('notify_when_changed') == 1) {
                \K::$fw->app_changed_fields[] = [
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
        return \Models\Main\Reports\Reports::getReportsQueryBoolean($options);
    }
}