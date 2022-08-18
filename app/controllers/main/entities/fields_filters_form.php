<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Fields_filters_form extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        //$fields_info_query = db_query("select * from app_fields where id='" . \K::$fw->GET['fields_id'] . "'");

        $fields_info = \K::model()->db_fetch_one('app_fields', [
            'id = ?',
            \K::$fw->GET['fields_id']
        ]);

        if (!$fields_info) {
            \Helpers\Urls::redirect_to('main/entities/fields', 'entities_id=' . \K::$fw->GET['entities_id']);
        }

        switch ($fields_info['type']) {
            case 'fieldtype_related_records':
                $reports_type = 'related_items_' . (int)\K::$fw->GET['fields_id'];
                break;
            default:
                $reports_type = 'fieldfilter' . (int)\K::$fw->GET['fields_id'];
                break;
        }

        /*$reports_info_query = db_query(
            "select * from app_reports where id='" . db_input(
                \K::$fw->GET['reports_id']
            ) . "' and reports_type='" . $reports_type . "'"
        );*/

        \K::$fw->reports_info = \K::model()->db_fetch_one('app_reports', [
            'id = ? and reports_type = ',
            \K::$fw->GET['reports_id'],
            $reports_type
        ], [], 'entities_id');

        if (!\K::$fw->reports_info) {
            echo \K::$fw->TEXT_REPORT_NOT_FOUND;
        } else {
            /*if (isset(\K::$fw->GET['id'])) {
                $obj = db_find('app_reports_filters', \K::$fw->GET['id']);
            } else {
                $obj = db_show_columns('app_reports_filters');
            }*/

            \K::$fw->obj = \K::model()->db_find('app_reports_filters', \K::$fw->GET['id']);

            \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'fields_filters_form.php';

            echo \K::view()->render(\K::$fw->subTemplate);
        }
    }
}