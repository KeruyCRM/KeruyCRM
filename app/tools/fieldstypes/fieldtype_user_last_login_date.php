<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_user_last_login_date
{
    public $options;

    public function __construct()
    {
        $this->options = [
            'name' => \K::$fw->TEXT_FIELDTYPE_USER_LAST_LOGIN_DATE,
            'title' => \K::$fw->TEXT_FIELDTYPE_USER_LAST_LOGIN_DATE
        ];
    }

    public function output($options)
    {
        if (strlen($options['value']) > 0 and $options['value'] != 0) {
            if (\K::$fw->app_user['group_id'] == 0) {
                return '<a href="' . \Helpers\Urls::url_for(
                        'main/tools/users_login_log',
                        'users_id=' . $options['item']['id']
                    ) . '" target="_new">' . \Helpers\App::format_date_time($options['value']) . '</a>';
            } else {
                return \Helpers\App::format_date_time($options['value']);
            }
        } else {
            return '';
        }
    }
}