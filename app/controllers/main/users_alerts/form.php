<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Users_alerts;

class Form extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Users_alerts\_Module::top();
    }

    public function index()
    {
        if (isset(\K::$fw->GET['id'])) {
            $obj = \K::model()->db_find('app_users_alerts', \K::$fw->GET['id']);
        } else {
            $obj = \K::model()->db_show_columns('app_users_alerts');
        }

        \K::$fw->obj = $obj;

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'form.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}