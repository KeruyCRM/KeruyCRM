<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Fields_form_internal extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        if (isset(\K::$fw->GET['id'])) {
            \K::$fw->obj = \K::model()->db_find('app_fields', \K::$fw->GET['id']);
        } else {
            \K::$fw->obj = \K::model()->db_show_columns('app_fields');
        }

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'fields_form_internal.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}