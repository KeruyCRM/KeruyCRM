<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Users_alerts;

class _Module
{
    public static function top()
    {
        if (\K::$fw->app_user['group_id'] > 0) {
            \Helpers\Urls::redirect_to('main/dashboard/access_forbidden');
        }
    }
}