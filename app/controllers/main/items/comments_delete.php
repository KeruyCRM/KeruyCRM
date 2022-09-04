<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Comments_delete extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();
    }

    public function index()
    {
        if (\Models\Main\Users\Users::has_comments_access('delete')) {
            \K::$fw->heading = \K::$fw->TEXT_HEADING_DELETE;
            \K::$fw->content = \K::$fw->TEXT_ARE_YOU_SURE;
            \K::$fw->button_title = \K::$fw->TEXT_BUTTON_DELETE;
        } else {
            \K::$fw->heading = \K::$fw->TEXT_WARNING;
            \K::$fw->content = \K::$fw->TEXT_NO_ACCESS;
            \K::$fw->button_title = 'hide-save-button';
        }

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'comments_delete.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}