<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Configuration;

class Attachments extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Configuration\_Module::top();
    }

    public function index()
    {
        $choices = [];
        $choices[''] = \K::$fw->TEXT_NONE;

        $fields_query = \K::model()->db_query_exec(
            "select f.id, f.name, e.name as entity_name from app_fields f, app_entities e where e.id = f.entities_id and type in ( ?, ?, ?, ? ) order by e.sort_order, e.name, f.name",
            [
                'fieldtype_attachments',
                'fieldtype_image',
                'fieldtype_image_ajax',
                'fieldtype_input_file'
            ]
        );
        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $choices[$fields['entity_name']][$fields['id']] = $fields['name'];
        }

        \K::$fw->choices = $choices;
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'attachments.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }
}