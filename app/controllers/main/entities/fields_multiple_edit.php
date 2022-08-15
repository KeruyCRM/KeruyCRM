<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Fields_multiple_edit extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        $choices = [];
        $choices['yes'] = \K::$fw->TEXT_YES;
        $choices['no'] = \K::$fw->TEXT_NO;

        \K::$fw->choices = $choices;

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'fields_multiple_edit.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}