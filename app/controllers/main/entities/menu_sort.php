<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Menu_sort extends \Controller
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
        \K::$fw->groups_query = \K::model()->db_fetch(
            'app_entities_menu', [],
            ['order' => 'sort_order,name'],
            'id,name'
        );

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'menu_sort.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}