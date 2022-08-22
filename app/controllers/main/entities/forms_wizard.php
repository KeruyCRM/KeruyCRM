<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Forms_wizard extends \Controller
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

        \K::$fw->default_selector = ['1' => \K::$fw->TEXT_YES, '0' => \K::$fw->TEXT_NO];

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'forms_wizard.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}