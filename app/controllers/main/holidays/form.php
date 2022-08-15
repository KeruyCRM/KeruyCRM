<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Holidays;

class Form extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Holidays\_Module::top();
    }

    public function index()
    {
        \K::$fw->obj = [];
        if (\K::$fw->GET['id']) {
            \K::$fw->obj = \K::model()->db_find('app_holidays', \K::$fw->GET['id']);
        } else {
            \K::$fw->obj = \K::model()->db_show_columns('app_holidays');
        }

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'form.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}