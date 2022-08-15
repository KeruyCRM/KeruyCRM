<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_user_language
{
    public $options;

    public function __construct()
    {
        $this->options = [
            'name' => \K::$fw->TEXT_FIELDTYPE_USER_LANGUAGE_TITLE,
            'title' => \K::$fw->TEXT_FIELDTYPE_USER_LANGUAGE_TITLE
        ];
    }

    public function render($field, $obj, $params = [])
    {
        $selected = (strlen(
            $obj['field_' . $field['id']]
        ) > 0 ? $obj['field_' . $field['id']] : \K::$fw->CFG_APP_LANGUAGE);

        return \Helpers\Html::select_tag(
            'fields[' . $field['id'] . ']',
            \Helpers\App::app_get_languages_choices(),
            $selected,
            ['class' => 'form-control input-medium required']
        );
    }

    public function process($options)
    {
        return \K::model()->db_prepare_input(str_replace(['..', '/', '\/'], '', $options['value']));
    }

    public function output($options)
    {
        return implode(' ', array_map('ucfirst', explode('_', substr($options['value'], 0, -4))));
    }
}