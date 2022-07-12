<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Configuration;

class Guest_login extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Configuration\_Module::top();
    }

    public function index()
    {
        $choices = [];
        $choices[0] = \K::$fw->TEXT_NONE;
        $users_query = \K::model()->db_query_exec(
            'select u.*, a.name as group_name from app_entity_1 u left join app_access_groups a on a.id = u.field_6 where field_6 > 0 order by u.field_8, u.field_7'
        );

        //while ($users = db_fetch_array($users_query)) {
        foreach ($users_query as $users) {
            $choices[$users['group_name']][$users['id']] = $users['field_8'] . ' ' . $users['field_7'];
        }

        \K::$fw->choices = $choices;

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'guest_login.php';

        echo \K::view()->render($this->app_layout);
    }
}