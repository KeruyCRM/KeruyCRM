<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Listing_filters extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();

        /*$reports_info_query = db_query(
            "select * from app_reports where entities_id='" . db_input(
                \K::$fw->GET['entities_id']
            ) . "' and reports_type='default'"
        );*/

        if (!\K::$fw->GET['entities_id']) {
            \Helpers\Urls::redirect_to('main/entities');//FIX
        }

        \K::$fw->reports_info = \K::model()->db_fetch_one('app_reports', [
            'entities_id = ? and reports_type = ?',
            \K::$fw->GET['entities_id'],
            'default'
        ]);

        if (!\K::$fw->reports_info) {
            $sql_data = [
                'name' => '',
                'entities_id' => \K::$fw->GET['entities_id'],
                'reports_type' => 'default',
                'in_menu' => 0,
                'in_dashboard' => 0,
                'created_by' => 0,
            ];

            \K::model()->db_perform('app_reports', $sql_data);

            \Helpers\Urls::redirect_to('main/entities/listing_filters', 'entities_id=' . \K::$fw->GET['entities_id']);
        }
    }

    public function index()
    {
        \K::$fw->reports_filters_count = \K::model()->db_count(
            'app_reports_filters',
            \K::$fw->reports_info['id'],
            'reports_id'
        );

        \K::$fw->filters_query = \K::model()->db_query_exec(
            "select rf.*, f.name, f.type from app_reports_filters rf left join app_fields f on rf.fields_id = f.id where rf.reports_id = ? order by rf.id",
            \K::$fw->reports_info['id'],
            'app_reports_filters,app_fields'
        );

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'listing_filters.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $values = '';

            if (isset(\K::$fw->POST['values'])) {
                if (is_array(\K::$fw->POST['values'])) {
                    $values = implode(',', \K::$fw->POST['values']);
                } else {
                    $values = \K::$fw->POST['values'];
                }
            }

            $sql_data = [
                'reports_id' => \K::$fw->GET['reports_id'],
                'fields_id' => \K::$fw->POST['fields_id'],
                'filters_condition' => (\K::$fw->POST['filters_condition'] ?? ''),
                'filters_values' => $values,
            ];

            /*if (isset(\K::$fw->GET['id'])) {
                db_perform('app_reports_filters', $sql_data, 'update', "id='" . db_input(\K::$fw->GET['id']) . "'");
            } else {
                db_perform('app_reports_filters', $sql_data);
            }*/

            \K::model()->db_perform('app_reports_filters', $sql_data, [
                'id = ?',
                \K::$fw->GET['id']
            ]);

            \Helpers\Urls::redirect_to(
                'main/entities/listing_filters',
                'reports_id=' . \K::$fw->GET['reports_id'] . '&entities_id=' . \K::$fw->GET['entities_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST') {
            if (isset(\K::$fw->GET['id'])) {
                //db_query("delete from app_reports_filters where id='" . db_input(\K::$fw->GET['id']) . "'");

                \K::model()->db_delete_row('app_reports_filters', \K::$fw->GET['id']);

                \K::flash()->addMessage(\K::$fw->TEXT_WARN_DELETE_FILTER_SUCCESS, 'success');

                \Helpers\Urls::redirect_to(
                    'main/entities/listing_filters',
                    'reports_id=' . \K::$fw->GET['reports_id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                );
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}