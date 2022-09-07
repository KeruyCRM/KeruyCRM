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
        //$reports_info_query = db_query("select * from app_reports where id='" . db_input($_GET['reports_id']) . "'");

        \K::$fw->reports_info = \K::model()->db_fetch_one('app_reports', [
            'id = ?',
            \K::$fw->GET['reports_id']
        ]);

        if (!\K::$fw->reports_info) {
            echo \K::$fw->TEXT_REPORT_NOT_FOUND;
        } else {
            \K::$fw->obj = \K::model()->db_find('app_reports_filters', \K::$fw->GET['id']);

            \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'filters_form.php';

            echo \K::view()->render(\K::$fw->app_layout);
        }
    }
}