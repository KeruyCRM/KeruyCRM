<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Configuration;

class Maintenance_mode extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Configuration\_Module::top();
    }

    public function index()
    {
        $choices = ['' => \K::$fw->TEXT_NONE];
        $users_query = \K::model()->db_query_exec(
            'select u.*, a.name as group_name from app_entity_1 u left join app_access_groups a on a.id = u.field_6 where u.field_6 > 0 and u.field_5 = 1 order by u.field_8, u.field_7'
        );

        //while ($users = db_fetch_array($users_query)) {
        foreach ($users_query as $users) {
            $choices[$users['group_name']][$users['id']] = \K::$fw->app_users_cache[$users['id']]['name'];
        }

        \K::$fw->choices = $choices;

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'maintenance_mode.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }
}