<?php

namespace Tools\FieldsTypes;

class Fieldtype_section
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_SECTION];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DESCRIPTION,
            'name' => 'description',
            'type' => 'textarea',
            'params' => ['class' => 'form-control']
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $count = (isset($params['count_fields']) ? 'form-section-' . $params['count_fields'] : '');

        $html = '<h3 class="form-section ' . $count . '">' . $field['name'] . '</h3>';

        if (strlen($cfg->get('description'))) {
            $html .= '<p class="form-section-description">' . $cfg->get('description') . '</p>';
        }

        return $html;
    }

    public function process($options)
    {
        return false;
    }

    public function output($options)
    {
        return '';
    }
}