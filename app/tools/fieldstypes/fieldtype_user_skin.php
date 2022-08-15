<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_user_skin
{
    public $options;

    public function __construct()
    {
        $this->options = [
            'name' => \K::$fw->TEXT_FIELDTYPE_USER_SKIN_TITLE,
            'title' => \K::$fw->TEXT_FIELDTYPE_USER_SKIN_TITLE
        ];
    }

    public function render($field, $obj, $params = [])
    {
        if (strlen(\K::$fw->CFG_APP_SKIN)) {
            return '<p class="form-control-static">' . \K::$fw->CFG_APP_SKIN . '</p>';
        } else {
            if (!strlen($obj['field_' . $field['id']])) {
                $obj['field_' . $field['id']] = 'default';
            }

            return \Helpers\Html::select_tag(
                'fields[' . $field['id'] . ']',
                \Helpers\App::app_get_skins_choices(false),
                $obj['field_' . $field['id']],
                ['class' => 'form-control input-medium']
            );
        }
    }

    public function process($options)
    {
        return $options['value'];
    }

    public function output($options)
    {
        return $options['value'];
    }
}