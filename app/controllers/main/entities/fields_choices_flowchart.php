<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Fields_choices_flowchart extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        \K::$fw->flowchart = new \Tools\Flowchart\Fields_choices_flowchart();
        \K::$fw->flowchart->prepare_data(\K::$fw->GET['fields_id']);

        \K::$fw->field_info = \K::model()->db_find('app_fields', \K::$fw->GET['fields_id']);

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'fields_choices_flowchart.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }
}