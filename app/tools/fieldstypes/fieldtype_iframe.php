<?php

namespace Tools\FieldsTypes;

class Fieldtype_iframe
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_IFRAME_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_WIDTH,
            'name' => 'input_width',
            'type' => 'dropdown',
            'choices' => [
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

        $cfg['Iframe'][] = [
            'title' => \K::$fw->TEXT_WIDTH,
            'name' => 'width',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg['Iframe'][] = [
            'title' => \K::$fw->TEXT_HEIGHT,
            'name' => 'height',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg['Iframe'][] = [
            'title' => \K::$fw->TEXT_SCROLL_BAR,
            'name' => 'scrolling',
            'type' => 'dropdown',
            'choices' => ['auto' => \K::$fw->TEXT_AUTOMATIC, 'no' => \K::$fw->TEXT_NO, 'yes' => \K::$fw->TEXT_YES],
            'tooltip_icon' => \K::$fw->TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg['Iframe'][] = [
            'title' => \K::$fw->TEXT_EXTRA_PARAMS,
            'name' => 'extra_params',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_FIELDTYPE_IFRAME_EXTRA_PARAMS_TIP,
            'params' => ['class' => 'form-control input-xlarge']
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Tools\Fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'form-control ' . $cfg->get('input_width') .
                ' fieldtype_iframe url field_' . $field['id'] .
                ($field['is_required'] == 1 ? ' required noSpace' : '')
        ];

        $attributes = fields_types::prepare_uniquer_error_msg_param($attributes, $cfg);

        return input_tag('fields[' . $field['id'] . ']', $obj['field_' . $field['id']], $attributes);
    }

    public function process($options)
    {
        return db_prepare_input($options['value']);
    }

    public function output($options)
    {
        $value = trim($options['value']);
        if (isset($options['is_export'])) {
            return $value;
        } elseif (strlen($value)) {
            $cfg = new \Tools\Fields_types_cfg($options['field']['configuration']);

            return '<iframe  src="' . $value . '" width="' . $cfg->get('width') . '"  height="' . $cfg->get(
                    'height'
                ) . '" scrolling="' . $cfg->get('scrolling') . '" ' . $cfg->get('extra_params') . '></iframe>';
        } else {
            return '';
        }
    }
}