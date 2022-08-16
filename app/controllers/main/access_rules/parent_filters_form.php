<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Access_rules;

class Parent_filters_form extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Access_rules\_Module::top();
    }

    public function index()
    {
        /*$reports_info_query = db_query(
            "select * from app_reports where id='" . db_input(
                $_GET['reports_id']
            ) . "' and reports_type='hide_add_button_rules" . $_GET['entities_id'] . "'"
        );*/

        $reports_info = \K::model()->db_fetch_one('app_reports', [
            'id = ? and reports_type = ?',
            \K::$fw->GET['reports_id'],
            'hide_add_button_rules' . \K::$fw->GET['entities_id']
        ]);

        if ($reports_info) {
            \K::$fw->reports_info = $reports_info;
            \K::$fw->obj = \K::model()->db_find('app_reports_filters', \K::$fw->GET['id']);

            \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'parent_filters_form.php';

            echo \K::view()->render(\K::$fw->subTemplate);
        } else {
            echo \K::$fw->TEXT_REPORT_NOT_FOUND;
        }
    }
}