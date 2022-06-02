<?php

class fieldtype_user_status
{
    public $options;

    function __construct()
    {
        $this->options = ['name' => TEXT_FIELDTYPE_USER_STATUS_TITLE, 'title' => TEXT_FIELDTYPE_USER_STATUS_TITLE];
    }

    function render($field, $obj, $params = [])
    {
        global $app_user;

        $value = $obj['field_' . $field['id']];
        if (strlen($value) == 0) {
            $value = 1;
        }

        if (isset($obj['id']) and $obj['id'] == $app_user['id'] and $obj['id'] > 0) {
            return '<p class="form-control-static">' . TEXT_ACTIVE . '</p>' . input_hidden_tag(
                    'fields[' . $field['id'] . ']',
                    $value
                );
        }

        return select_tag(
                'fields[' . $field['id'] . ']',
                ['1' => TEXT_ACTIVE, '0' => TEXT_INACTIVE],
                $value,
                ['class' => 'form-control input-medium']
            ) . tooltip_text(TEXT_FIELDTYPE_USER_STATUS_TOOLTIP);
    }

    function process($options)
    {
        return $options['value'];
    }

    function output($options)
    {
        $html = '';

        switch (true) {
            case ($options['value'] == 1 and $options['item']['is_email_verified'] == 1):
                $html = '<span class="label label-success">' . TEXT_ACTIVE . '</span>';
                break;
            case ($options['value'] == 1 and $options['item']['is_email_verified'] == 0):
                $html = '<span id="user_status_' . $options['item']['id'] . '" class="label label-warning" title="' . addslashes(
                        TEXT_EMAIL_NOT_VERIFIED
                    ) . '">' . TEXT_ACTIVE . '</span>';
                break;
            case ($options['value'] == 0):
                $html = '<span class="label label-default">' . TEXT_INACTIVE . '</span>';
                break;
        }

        return $html;
    }

    function reports_query($options)
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