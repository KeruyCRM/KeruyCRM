<?php

namespace Tools\FieldsTypes;

class Fieldtype_textarea
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_TEXTAREA_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_WIDTH,
            'name' => 'width',
            'type' => 'dropdown',
            'choices' => [
                'input-small' => \K::$fw->TEXT_INPUT_SMALL,
                'input-medium' => \K::$fw->TEXT_INPUT_MEDIUM,
                'input-large' => \K::$fw->TEXT_INPUT_LARGE,
                'input-xlarge' => \K::$fw->TEXT_INPUT_XLARGE
            ],
            'tooltip' => \K::$fw->TEXT_ENTER_WIDTH,
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
        $cfg = fields_types::parse_configuration($field['configuration']);

        $attributes = [
            'rows' => '3',
            'class' => 'form-control ' . $cfg['width'] . ($field['is_heading'] == 1 ? ' autofocus' : '') . ' fieldtype_textarea field_' . $field['id'] . ($field['is_required'] == 1 ? ' required noSpace' : '')
        ];

        return textarea_tag(
            'fields[' . $field['id'] . ']',
            str_replace(['&lt;', '&gt;'], ['<', '>'], $obj['field_' . $field['id']]),
            $attributes
        );
    }

    public function process($options)
    {
        return str_replace(['<', '>'], ['&lt;', '&gt;'], $options['value']);
    }

    public function output($options)
    {
        if (isset($options['is_export'])) {
            return (!isset($options['is_print']) ? str_replace(['&lt;', '&gt;'], ['<', '>'], $options['value']) : nl2br(
                $options['value']
            ));
        } else {
            return auto_link_text(nl2br($options['value']));
        }
    }
}