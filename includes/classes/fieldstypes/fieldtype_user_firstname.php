<?php

class fieldtype_user_firstname
{
    public $options;

    function __construct()
    {
        $this->options = [
            'name' => TEXT_FIELDTYPE_USER_FIRSTNAME_TITLE,
            'title' => TEXT_FIELDTYPE_USER_FIRSTNAME_TITLE
        ];
    }

    function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => TEXT_ALLOW_SEARCH_TIP
        ];

        return $cfg;
    }

    function render($field, $obj, $params = [])
    {
        return input_tag(
            'fields[' . $field['id'] . ']',
            $obj['field_' . $field['id']],
            ['class' => 'form-control input-medium required noSpace']
        );
    }

    function process($options)
    {
        return db_prepare_input($options['value']);
    }

    function output($options)
    {
        return $options['value'];
    }
}