<?php

class fieldtype_section
{
    public $options;

    function __construct()
    {
        $this->options = ['title' => TEXT_FIELDTYPE_SECTION];
    }

    function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => TEXT_DESCRIPTION,
            'name' => 'description',
            'type' => 'textarea',
            'params' => ['class' => 'form-control']
        ];

        return $cfg;
    }

    function render($field, $obj, $params = [])
    {
        $cfg = new fields_types_cfg($field['configuration']);

        $count = (isset($params['count_fields']) ? 'form-section-' . $params['count_fields'] : '');

        $html = '<h3 class="form-section ' . $count . '">' . $field['name'] . '</h3>';

        if (strlen($cfg->get('description'))) {
            $html .= '<p class="form-section-description">' . $cfg->get('description') . '</p>';
        }

        return $html;
    }

    function process($options)
    {
        return false;
    }

    function output($options)
    {
        return '';
    }
}