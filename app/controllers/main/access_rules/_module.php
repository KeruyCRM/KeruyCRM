<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Access_rules;

class _Module
{
    public static function top()
    {
        if (\K::$fw->app_user['group_id'] > 0) {
            \Helpers\Urls::redirect_to('main/dashboard/access_forbidden');
        }

        $app_title = app_set_title(TEXT_ACCESS_ALLOCATION_RULES);

        //check if entity exist
        if (isset($_GET['entities_id'])) {
            $check_query = db_query("select * from app_entities where id='" . db_input($_GET['entities_id']) . "'");
            if (!$check = db_fetch_array($check_query)) {
                redirect_to('entities/entities');
            }
        }
    }
}