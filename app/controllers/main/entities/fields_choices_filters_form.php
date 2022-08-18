<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Fields_choices_filters_form extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        /*$reports_info_query = db_query(
            "select * from app_reports where id='" . db_input(
                $_GET['reports_id']
            ) . "' and reports_type='fields_choices" . $_GET['choices_id'] . "'"
        );*/

        \K::$fw->reports_info = \K::model()->db_fetch_one('app_reports', [
            'id = ? and reports_type = ?',
            \K::$fw->GET['reports_id'],
            'fields_choices' . \K::$fw->GET['choices_id']
        ], [], 'entities_id');

        if (!\K::$fw->reports_info) {
            echo \K::$fw->TEXT_REPORT_NOT_FOUND;
        } else {
            /*if (isset($_GET['id'])) {
                $obj = db_find('app_reports_filters', $_GET['id']);
            } else {
                $obj = db_show_columns('app_reports_filters');
            }*/

            \K::$fw->obj = \K::model()->db_find('app_reports_filters', \K::$fw->GET['id']);

            \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'fields_choices_filters_form.php';

            echo \K::view()->render(\K::$fw->subTemplate);
        }
    }
}