<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Holidays;

class _Module
{
    public static function top()
    {
        if (\K::$fw->app_user['group_id'] > 0) {
            \Helpers\Urls::redirect_to('main/dashboard/access_forbidden');
        }

        \K::$fw->app_title = \Helpers\App::app_set_title(\K::$fw->TEXT_HOLIDAYS);
    }
}