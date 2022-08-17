<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Fields_ajax_configuration extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        if (isset(\K::$fw->POST['field_type'])) {
            $class = \K::$fw->POST['field_type'];

            if (class_exists($class)) {
                $field_type = new $class();

                if (method_exists($field_type, 'get_ajax_configuration')) {
                    echo \Models\Main\Fields_types::render_configuration(
                        $field_type->get_ajax_configuration(\K::$fw->POST['name'], \K::$fw->POST['value']),
                        \K::$fw->POST['id']
                    );
                }
            }
        }
    }
}