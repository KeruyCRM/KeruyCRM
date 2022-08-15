<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Fields_delete extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        if (isset(\K::$fw->GET['entities_id']) and isset(\K::$fw->GET['id'])) {
            $msg = \Models\Main\Fields::check_before_delete(\K::$fw->GET['entities_id'], \K::$fw->GET['id']);

            if (strlen($msg) > 0) {
                \K::$fw->heading = \K::$fw->TEXT_WARNING;
                \K::$fw->content = $msg;
                \K::$fw->button_title = 'hide-save-button';
            } else {
                \K::$fw->heading = \K::$fw->TEXT_HEADING_DELETE;
                \K::$fw->content = sprintf(
                        \K::$fw->TEXT_DEFAULT_DELETE_CONFIRMATION,
                        \Models\Main\Fields::get_name_by_id(\K::$fw->GET['id'])
                    ) . '<br><br><p class="alert alert-warning">' . \K::$fw->TEXT_DELETE_FIELD_WARNING . '</p>';
                \K::$fw->button_title = \K::$fw->TEXT_BUTTON_DELETE;
            }

            \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'fields_delete.php';

            echo \K::view()->render(\K::$fw->subTemplate);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}