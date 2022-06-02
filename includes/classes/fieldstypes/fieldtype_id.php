<?php

class fieldtype_id
{
    public $options;

    function __construct()
    {
        $this->options = ['name' => TEXT_FIELDTYPE_ID_TITLE, 'title' => TEXT_FIELDTYPE_ID_TITLE];
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

    function output($options)
    {
        return $options['value'];
    }
}