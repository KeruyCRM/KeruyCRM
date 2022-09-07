<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Filters_form extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();
    }

    public function index()
    {
        $reports_info_query = db_query("select * from app_reports where id='" . db_input($_GET['reports_id']) . "'");
        if (!$reports_info = db_fetch_array($reports_info_query)) {
            echo TEXT_REPORT_NOT_FOUND;
        }else {
            \K::$fw->obj = db_find('app_reports_filters', $_GET['id']);

            \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'filters_form.php';

            echo \K::view()->render(\K::$fw->app_layout);
        }
    }
}