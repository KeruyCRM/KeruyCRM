<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class _Module
{
    public static function top()
    {
        //check access
        if (\K::$fw->app_user['group_id'] > 0) {
            \Helpers\Urls::redirect_to('main/dashboard/access_forbidden');
        }

        \K::$fw->app_title = \Helpers\App::app_set_title(\K::$fw->TEXT_ENTITIES_HEADING);

        //check if entity exist
        if (isset(\K::$fw->GET['entities_id'])) {
            //$check_query = db_query("select * from app_entities where id='" . db_input(\K::$fw->GET['entities_id']) . "'");

            $check = \K::model()->db_fetch_one('app_entities',[
                'id = ?',
                \K::$fw->GET['entities_id']
            ]);

            if (!$check) {
                \Helpers\Urls::redirect_to('main/entities/entities');
            }
        }

        //check privileges
        \K::model()->db_check_privileges();
    }
}