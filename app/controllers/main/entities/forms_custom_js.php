<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Forms_custom_js extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        \K::$fw->cfg = new \Models\Main\Entities_cfg(\K::$fw->GET['entities_id']);

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'forms_custom_js.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}