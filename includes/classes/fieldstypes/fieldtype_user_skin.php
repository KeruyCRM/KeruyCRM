<?php

class fieldtype_user_skin
{
    public $options;

    function __construct()
    {
        $this->options = ['name' => TEXT_FIELDTYPE_USER_SKIN_TITLE, 'title' => TEXT_FIELDTYPE_USER_SKIN_TITLE];
    }

    function render($field, $obj, $params = [])
    {
        if (strlen(CFG_APP_SKIN)) {
            return '<p class="form-control-static">' . CFG_APP_SKIN . '</p>';
        } else {
            if (!strlen($obj['field_' . $field['id']])) {
                $obj['field_' . $field['id']] = 'default';
            }

            return select_tag(
                'fields[' . $field['id'] . ']',
                app_get_skins_choices(false),
                $obj['field_' . $field['id']],
                ['class' => 'form-control input-medium']
            );
        }
    }

    function process($options)
    {
        return $options['value'];
    }

    function output($options)
    {
        return $options['value'];
    }
}