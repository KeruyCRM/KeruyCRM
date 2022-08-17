<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Fields_choices_delete extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        $msg = \Models\Main\Fields_choices::check_before_delete(\K::$fw->GET['id']);

        if (strlen($msg) > 0) {
            \K::$fw->heading = \K::$fw->TEXT_WARNING;
            \K::$fw->content = $msg;
            \K::$fw->button_title = false;
        } else {
            \K::$fw->heading = \K::$fw->TEXT_HEADING_DELETE;
            \K::$fw->content = sprintf(
                \K::$fw->TEXT_DEFAULT_DELETE_CONFIRMATION,
                \Models\Main\Fields_choices::get_name_by_id(\K::$fw->GET['id'])
            );
            \K::$fw->button_title = \K::$fw->TEXT_BUTTON_DELETE;
        }

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'fields_choices_delete.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}