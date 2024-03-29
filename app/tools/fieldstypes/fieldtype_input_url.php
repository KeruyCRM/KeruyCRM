<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_input_url
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_INPUT_URL_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_TARGET,
            'name' => 'target',
            'type' => 'dropdown',
            'choices' => ['_blank' => \K::$fw->TEXT_TARGET_BLANK, '_self' => \K::$fw->TEXT_TARGET_SELF],
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[] = ['title' => \K::$fw->TEXT_VALIDATE_URL, 'name' => 'validate_url', 'type' => 'checkbox'];

        $cfg[] = [
            'title' => \K::$fw->TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_ALLOW_SEARCH_TIP
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_URL_PREVIEW_TEXT,
            'name' => 'preview_text',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_URL_PREVIEW_TEXT_TIP,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_URL_PREFIX,
            'name' => 'prefix',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_URL_PREFIX_TIP,
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_IS_UNIQUE_FIELD_VALUE,
            'name' => 'is_unique',
            'type' => 'dropdown',
            'choices' => \Models\Main\Fields_types::get_is_unique_choices(\K::$fw->POST['entities_id']),
            'tooltip_icon' => \K::$fw->TEXT_IS_UNIQUE_FIELD_VALUE_TIP,
            'params' => ['class' => 'form-control input-large']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_ERROR_MESSAGE,
            'name' => 'unique_error_msg',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_UNIQUE_FIELD_VALUE_ERROR_MSG_TIP,
            'tooltip' => \K::$fw->TEXT_DEFAULT . ': ' . \K::$fw->TEXT_UNIQUE_FIELD_VALUE_ERROR,
            'params' => ['class' => 'form-control input-xlarge']
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'form-control input-large fieldtype_input_url field_' . $field['id'] .
                ($field['is_required'] == 1 ? ' required noSpace' : '') .
                ($cfg->get('is_unique') > 0 ? ' is-unique' : '') .
                ($cfg->get('validate_url') == 1 ? ' url' : '')
        ];

        $attributes = \Models\Main\Fields_types::prepare_uniquer_error_msg_param($attributes, $cfg);

        return \Helpers\Html::input_tag('fields[' . $field['id'] . ']', $obj['field_' . $field['id']], $attributes);
    }

    public function process($options)
    {
        return \K::model()->db_prepare_input($options['value']);
    }

    public function output($options)
    {
        $cfg = new \Tools\Settings($options['field']['configuration']);

        $url = $options['value'];
        $url_text = \K::$fw->TEXT_VIEW;

        if ($cfg->get('preview_text') == 'none') {
            $url_text = $url;
        } elseif (strlen($cfg->get('preview_text')) > 0) {
            $url_text = $cfg->get('preview_text');
        }

        if (strlen($cfg->get('prefix')) > 0) {
            $url = (!stristr($url, $cfg->get('prefix')) ? $cfg->get('prefix') : '') . $url;
        } elseif (!stristr($url, '://')) {
            $url = 'http://' . $url;
        }

        if (strlen($options['value']) > 0) {
            if (isset($options['is_export'])) {
                return $url;
            } else {
                return '<a href="' . $url . '" target="' . $cfg->get('target', '_blank') . '">' . $url_text . '</a>';
            }
        } else {
            return '';
        }
    }
}