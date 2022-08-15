<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Menu_form extends \Controller
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
            $obj = \K::model()->db_find('app_entities_menu', \K::$fw->GET['id']);
        } else {
            $obj = \K::model()->db_show_columns('app_entities_menu');

            $obj['parent_id'] = (\K::$fw->GET['parent_id'] ?? 0);
        }

        \K::$fw->obj = $obj;

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'menu_form.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}