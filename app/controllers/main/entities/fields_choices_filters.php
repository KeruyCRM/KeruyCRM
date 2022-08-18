<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Fields_choices_filters extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();

        //$fields_info_query = db_query("select * from app_fields where id='" . \K::$fw->GET['fields_id'] . "'");

        \K::$fw->fields_info = \K::model()->db_fetch_one('app_fields', [
            'id = ?',
            \K::$fw->GET['fields_id']
        ], [], 'id,name');

        if (!\K::$fw->fields_info) {
            \Helpers\Urls::redirect_to('main/entities/fields', 'entities_id=' . \K::$fw->GET['entities_id']);
        }

        $reports_type = 'fields_choices' . (int)\K::$fw->GET['choices_id'];

        /*$reports_info_query = db_query(
            "select * from app_reports where entities_id='" . db_input(
                \K::$fw->GET['entities_id']
            ) . "' and reports_type='{$reports_type}'"
        );*/

        \K::$fw->reports_info = \K::model()->db_fetch_one('app_reports', [
            'entities_id = ? and reports_type = ?',
            \K::$fw->GET['entities_id'],
            $reports_type
        ], [], 'id');

        if (!\K::$fw->reports_info) {
            $sql_data = [
                'name' => '',
                'entities_id' => \K::$fw->GET['entities_id'],
                'reports_type' => $reports_type,
                'in_menu' => 0,
                'in_dashboard' => 0,
                'created_by' => 0,
            ];

            \K::model()->db_perform('app_reports', $sql_data);

            \Helpers\Urls::redirect_to(
                'main/entities/fields_choices_filters',
                'choices_id=' . \K::$fw->GET['choices_id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
            );
        }
    }

    public function index()
    {
        \K::$fw->choices_info = \K::model()->db_find('app_fields_choices', \K::$fw->GET['choices_id']);

        \K::$fw->count = \K::model()->db_count('app_reports_filters', \K::$fw->reports_info['id'], 'reports_id');

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'fields_choices_filters.php';

        \K::$fw->filters_query = \K::model()->db_query(
            "select rf.*, f.name, f.type from app_reports_filters rf left join app_fields f on rf.fields_id = f.id where rf.reports_id = ? order by rf.id",
            \K::$fw->reports_info['id'],
            'app_reports_filters,app_fields'
        );

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        //TODO Refactoring field for redirect
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['choices_id']) and isset(\K::$fw->GET['entities_id']) and isset(\K::$fw->GET['fields_id'])) {
            $values = '';

            if (isset(\K::$fw->POST['values'])) {
                if (is_array(\K::$fw->POST['values'])) {
                    $values = implode(',', \K::$fw->POST['values']);
                } else {
                    $values = \K::$fw->POST['values'];
                }
            }

            $sql_data = [
                'reports_id' => \K::$fw->reports_info['id'],
                'fields_id' => \K::$fw->POST['fields_id'],
                'filters_condition' => \K::$fw->POST['filters_condition'],
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
                'main/entities/fields_choices_filters',
                'choices_id=' . \K::$fw->GET['choices_id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['id'])) {
            //db_query("delete from app_reports_filters where id='" . db_input(\K::$fw->GET['id']) . "'");

            \K::model()->db_delete_row('app_reports_filters', \K::$fw->GET['id']);

            \K::flash()->addMessage(\K::$fw->TEXT_WARN_DELETE_FILTER_SUCCESS, 'success');

            \Helpers\Urls::redirect_to(
                'main/entities/fields_choices_filters',
                'choices_id=' . \K::$fw->GET['choices_id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}