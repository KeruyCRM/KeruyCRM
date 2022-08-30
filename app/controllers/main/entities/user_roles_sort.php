<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class User_roles_sort extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        /*$choices_query = db_query(
            "select * from app_user_roles where fields_id = '" . db_input(
                _get::int('fields_id')
            ) . "' order by sort_order, name"
        );*/

        \K::$fw->choices_query = \K::model()->db_fetch('app_user_roles', [
            'fields_id = ?',
            \K::$fw->GET['fields_id']
        ], ['order' => 'sort_order,name'], 'id,name');

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'user_roles_sort.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }
}