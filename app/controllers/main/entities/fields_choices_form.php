<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Fields_choices_form extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        $obj = \K::model()->db_find('app_fields_choices', \K::$fw->GET['id']);

        if (!isset(\K::$fw->GET['id'])) {
            $obj['is_active'] = 1;
        }

        \K::$fw->obj = $obj;

        \K::$fw->fields_info = \K::model()->db_find('app_fields', \K::$fw->GET['fields_id']);

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'fields_choices_form.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}