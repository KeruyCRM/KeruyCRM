<?php

namespace Tools\FieldsTypes;

class Fieldtype_user_lastname
{
    public $options;

    public function __construct()
    {
        $this->options = [
            'name' => \K::$fw->TEXT_FIELDTYPE_USER_LASTNAME_TITLE,
            'title' => \K::$fw->TEXT_FIELDTYPE_USER_LASTNAME_TITLE
        ];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_ALLOW_SEARCH_TIP
        ];
        $cfg[] = ['title' => \K::$fw->TEXT_DISABLE, 'name' => 'is_disabled', 'type' => 'checkbox'];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new fields_types_cfg($field['configuration']);

        $html = '';
        $requried_class = 'required';

        if ($cfg->get('is_disabled') == 1) {
            $requried_class = '';
            $html = '<style>.form-group-8{display:none}</style>';
            $obj['field_' . $field['id']] = '';
        }

        return input_tag(
                'fields[' . $field['id'] . ']',
                $obj['field_' . $field['id']],
                ['class' => 'form-control input-medium noSpace ' . $requried_class]
            ) . $html;
    }

    public function process($options)
    {
        return db_prepare_input($options['value']);
    }

    public function output($options)
    {
        return $options['value'];
    }
}