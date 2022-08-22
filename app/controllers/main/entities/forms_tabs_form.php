<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Forms_tabs_form extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        \K::$fw->obj = \K::model()->db_find('app_forms_tabs', \K::$fw->GET['id']);

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'forms_tabs_form.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}