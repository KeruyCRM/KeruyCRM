<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Configuration;

class Social_login extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Configuration\_Module::top();
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'social_login.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }
}