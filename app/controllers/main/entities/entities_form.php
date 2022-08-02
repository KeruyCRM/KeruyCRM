<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Entities_form extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        if (isset(\K::$fw->GET['id'])) {
            $obj = \K::model()->db_find('app_entities', \K::$fw->GET['id']);
        } else {
            $obj = \K::model()->db_show_columns('app_entities');
            $obj['sort_order'] = 0;
        }

        \K::$fw->obj = $obj;

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'entities_form.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}