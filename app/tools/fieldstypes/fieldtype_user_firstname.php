<?php

namespace Tools\FieldsTypes;

class Fieldtype_user_firstname
{
    public $options;

    public function __construct()
    {
        $this->options = [
            'name' => \K::f3()->TEXT_FIELDTYPE_USER_FIRSTNAME_TITLE,
            'title' => \K::f3()->TEXT_FIELDTYPE_USER_FIRSTNAME_TITLE
        ];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::f3()->TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => \K::f3()->TEXT_ALLOW_SEARCH_TIP
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        return input_tag(
            'fields[' . $field['id'] . ']',
            $obj['field_' . $field['id']],
            ['class' => 'form-control input-medium required noSpace']
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