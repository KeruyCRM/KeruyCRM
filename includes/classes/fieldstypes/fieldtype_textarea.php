<?php

class fieldtype_textarea
{
    public $options;

    function __construct()
    {
        $this->options = ['title' => TEXT_FIELDTYPE_TEXTAREA_TITLE];
    }

    function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => TEXT_WIDTH,
            'name' => 'width',
            'type' => 'dropdown',
            'choices' => [
                'input-small' => TEXT_INPUT_SMALL,
                'input-medium' => TEXT_INPUT_MEDIUM,
                'input-large' => TEXT_INPUT_LARGE,
                'input-xlarge' => TEXT_INPUT_XLARGE
            ],
            'tooltip' => TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[] = [
            'title' => TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => TEXT_ALLOW_SEARCH_TIP
        ];

        $cfg[] = [
            'title' => TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        return $cfg;
    }

    function render($field, $obj, $params = [])
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

    function process($options)
    {
        return str_replace(['<', '>'], ['&lt;', '&gt;'], $options['value']);
    }

    function output($options)
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