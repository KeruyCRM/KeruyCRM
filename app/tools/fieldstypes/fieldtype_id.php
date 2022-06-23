<?php

namespace Tools\FieldsTypes;

class Fieldtype_id
{
    public $options;

    public function __construct()
    {
        $this->options = ['name' => \K::f3()->TEXT_FIELDTYPE_ID_TITLE, 'title' => \K::f3()->TEXT_FIELDTYPE_ID_TITLE];
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

    public function output($options)
    {
        return $options['value'];
    }
}