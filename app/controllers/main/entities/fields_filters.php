<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Fields_filters extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();

        //\K::$fw->fields_info_query = db_query("select * from app_fields where id='" . \K::$fw->GET['fields_id'] . "'");

        \K::$fw->fields_info = \K::model()->db_fetch_one('app_fields', [
            'id = ?',
            \K::$fw->GET['fields_id']
        ], [], 'configuration,type,name');

        if (!\K::$fw->fields_info) {
            \Helpers\Urls::redirect_to('main/entities/fields', 'entities_id=' . \K::$fw->GET['entities_id']);
        }

        $cfg = new \Models\Main\Fields_types_cfg(\K::$fw->fields_info['configuration']);

        switch (\K::$fw->fields_info['type']) {
            case 'fieldtype_related_records':
                $reports_type = 'related_items_' . (int)\K::$fw->GET['fields_id'];
                break;
            default:
                $reports_type = 'fieldfilter' . (int)\K::$fw->GET['fields_id'];
                break;
        }

        /*\K::$fw->reports_info_query = db_query(
            "select * from app_reports where entities_id='" . db_input(
                \K::$fw->GET['entities_id']
            ) . "' and reports_type='{$reports_type}'"
        );*/

        \K::$fw->reports_info = \K::model()->db_fetch_one('app_reports', [
            'entities_id = ? and reports_type = ?',
            \K::$fw->GET['entities_id'],
            $reports_type
        ]);

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
                'main/entities/fields_filters',
                'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
            );
        }
    }

    public function index()
    {
        $description = '';
        switch (\K::$fw->fields_info['type']) {
            case 'fieldtype_users_approve':
                $description = \K::$fw->TEXT_FIELDTYPE_USERS_APPROVE_FILTERS_INFO;
                break;
            case 'fieldtype_signature':
            case 'fieldtype_digital_signature':
                $description = \K::$fw->TEXT_SET_FILTERS_FOR_ACTION_BUTTON;
                break;
        }

        \K::$fw->description = $description;

        \K::$fw->count = \K::model()->db_count('app_reports_filters', \K::$fw->reports_info['id'], 'reports_id');

        \K::$fw->filters_query = \K::model()->db_query_exec(
            "select rf.*, f.name, f.type from app_reports_filters rf left join app_fields f on rf.fields_id = f.id where rf.reports_id = ? order by rf.id",
            \K::$fw->reports_info['id'],
            'app_reports_filters,app_fields'
        );

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'fields_filters.php';

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
                'main/entities/fields_filters',
                'reports_id=' . \K::$fw->GET['reports_id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
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
                'main/entities/fields_filters',
                'reports_id=' . \K::$fw->GET['reports_id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}