<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Parent_infopage_filters_form extends \Controller
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
            ) . "' and reports_type='parent_item_info_page'"
        );
        if (!$reports_info = db_fetch_array($reports_info_query)) {
            echo TEXT_REPORT_NOT_FOUND;
            exit();
        }

        $obj = [];

        if (isset($_GET['id'])) {
            $obj = db_find('app_reports_filters', $_GET['id']);
        } else {
            $obj = db_show_columns('app_reports_filters');
        }

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'parent_infopage_filters_form.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}