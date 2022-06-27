<?php

namespace Tools\FieldsTypes;

class Fieldtype_user_username
{
    public $options;

    public function __construct()
    {
        $this->options = [
            'name' => \K::$fw->TEXT_FIELDTYPE_USER_USERNAME_TITLE,
            'title' => \K::$fw->TEXT_FIELDTYPE_USER_USERNAME_TITLE
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

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        return input_tag(
            'fields[' . $field['id'] . ']',
            $obj['field_' . $field['id']],
            ['class' => 'form-control input-medium required noSpace', 'autocomplete' => 'off']
        );
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