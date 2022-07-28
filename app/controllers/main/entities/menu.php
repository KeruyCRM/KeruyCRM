<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Menu extends \Controller
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
        \K::$fw->countMenu = \K::model()->db_count('app_entities_menu');

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'menu.php';

        echo \K::view()->render($this->app_layout);
    }
}