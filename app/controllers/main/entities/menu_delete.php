<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Menu_delete extends \Controller
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
        \K::$fw->obj = \K::model()->db_fetch_one('app_entities_menu', [
            'id = ?',
            \K::$fw->GET['id']
        ], [], 'name');

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'menu_delete.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}