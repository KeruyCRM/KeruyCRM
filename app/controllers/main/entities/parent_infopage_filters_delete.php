<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Parent_infopage_filters_delete extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'parent_infopage_filters_delete.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}