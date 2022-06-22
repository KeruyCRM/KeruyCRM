<?php

namespace Tools\FieldsTypes;

class Fieldtype_user_status
{
    public $options;

    public function __construct()
    {
        $this->options = [
            'name' => \K::f3()->TEXT_FIELDTYPE_USER_STATUS_TITLE,
            'title' => \K::f3()->TEXT_FIELDTYPE_USER_STATUS_TITLE
        ];
    }

    public function render($field, $obj, $params = [])
    {
        global $app_user;

        $value = $obj['field_' . $field['id']];
        if (strlen($value) == 0) {
            $value = 1;
        }

        if (isset($obj['id']) and $obj['id'] == $app_user['id'] and $obj['id'] > 0) {
            return '<p class="form-control-static">' . \K::f3()->TEXT_ACTIVE . '</p>' . input_hidden_tag(
                    'fields[' . $field['id'] . ']',
                    $value
                );
        }

        return select_tag(
                'fields[' . $field['id'] . ']',
                ['1' => \K::f3()->TEXT_ACTIVE, '0' => \K::f3()->TEXT_INACTIVE],
                $value,
                ['class' => 'form-control input-medium']
            ) . tooltip_text(\K::f3()->TEXT_FIELDTYPE_USER_STATUS_TOOLTIP);
    }

    public function process($options)
    {
        return $options['value'];
    }

    public function output($options)
    {
        $html = '';

        switch (true) {
            case ($options['value'] == 1 and $options['item']['is_email_verified'] == 1):
                $html = '<span class="label label-success">' . \K::f3()->TEXT_ACTIVE . '</span>';
                break;
            case ($options['value'] == 1 and $options['item']['is_email_verified'] == 0):
                $html = '<span id="user_status_' . $options['item']['id'] . '" class="label label-warning" title="' . addslashes(
                        \K::f3()->TEXT_EMAIL_NOT_VERIFIED
                    ) . '">' . \K::f3()->TEXT_ACTIVE . '</span>';
                break;
            case ($options['value'] == 0):
                $html = '<span class="label label-default">' . \K::f3()->TEXT_INACTIVE . '</span>';
                break;
        }

        return $html;
    }

    public function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $sql = [];

        if (strlen($filters['filters_values']) > 0) {
            $sql_query[] = "(e.field_5 " . ($filters['filters_condition'] == 'include' ? 'in' : 'not in') . " (" . $filters['filters_values'] . "))";
        }

        return $sql_query;
    }
}