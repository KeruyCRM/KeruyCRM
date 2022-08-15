<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Entities_delete extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        $msg = \Models\Main\Entities::check_before_delete($_GET['id']);

        if (strlen($msg) > 0) {
            \K::$fw->heading = \K::$fw->TEXT_WARNING;
            \K::$fw->content = $msg;
            \K::$fw->button_title = 'hide-save-button';
        } else {
            \K::$fw->heading = \K::$fw->TEXT_HEADING_DELETE;
            \K::$fw->content = sprintf(
                \K::$fw->TEXT_DEFAULT_DELETE_CONFIRMATION,
                \Models\Main\Entities::get_name_by_id(\K::$fw->GET['id'])
            );
            \K::$fw->button_title = \K::$fw->TEXT_BUTTON_DELETE;
        }

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'entities_delete.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}