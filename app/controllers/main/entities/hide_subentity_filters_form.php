<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Hide_subentity_filters_form extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        $reports_info_query = db_query(
            "select * from app_reports where id='" . db_input(
                $_GET['reports_id']
            ) . "' and reports_type='hide_subentity_" . _GET('entities_id') . "'"
        );
        if (!$reports_info = db_fetch_array($reports_info_query)) {
            return TEXT_REPORT_NOT_FOUND;
        }

        $obj = [];

        if (isset($_GET['id'])) {
            $obj = db_find('app_reports_filters', $_GET['id']);
        } else {
            $obj = db_show_columns('app_reports_filters');
        }

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'hide_subentity_filters_form.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }
}