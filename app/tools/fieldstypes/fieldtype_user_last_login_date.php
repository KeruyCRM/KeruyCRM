<?php

namespace Tools\FieldsTypes;

class Fieldtype_user_last_login_date
{
    public $options;

    public function __construct()
    {
        $this->options = [
            'name' => \K::f3()->TEXT_FIELDTYPE_USER_LAST_LOGIN_DATE,
            'title' => \K::f3()->TEXT_FIELDTYPE_USER_LAST_LOGIN_DATE
        ];
    }

    public function output($options)
    {
        global $app_user;

        if (strlen($options['value']) > 0 and $options['value'] != 0) {
            if ($app_user['group_id'] == 0) {
                return '<a href="' . url_for(
                        'tools/users_login_log',
                        'users_id=' . $options['item']['id']
                    ) . '" target="_new">' . format_date_time($options['value']) . '</a>';
            } else {
                return format_date_time($options['value']);
            }
        } else {
            return '';
        }
    }
}