<?php

namespace Tools\FieldsTypes;

class Fieldtype_textarea_wysiwyg
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_TEXTAREA_WYSIWYG_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];
        $cfg[] = [
            'title' => \K::$fw->TEXT_TOOLBAR,
            'name' => 'toolbar',
            'type' => 'dropdown',
            'choices' => ['' => \K::$fw->TEXT_DEFAULT, 'small' => \K::$fw->TEXT_IN_ONE_LINE],
            'params' => ['class' => 'form-control input-medium']
        ];
        $cfg[] = [
            'title' => \K::$fw->TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_ALLOW_SEARCH_TIP
        ];
        $cfg[] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'form-control editor field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : ''),
            'toolbar' => $cfg->get('toolbar'),
        ];

        return textarea_tag('fields[' . $field['id'] . ']', $obj['field_' . $field['id']], $attributes);
    }

    public function process($options)
    {
        return db_prepare_html_input($options['value']);
    }

    public function output($options)
    {
        if (isset($options['is_export'])) {
            return (!isset($options['is_print']) ? str_replace(['&lt;', '&gt;'],
                ['<', '>'],
                $options['value']) : $options['value']);
        } else {
            return auto_link_text($options['value']);
        }
    }
}