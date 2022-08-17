<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Entityfield_filters extends \Controller
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

        \K::$fw->fields_info = \K::model()->db_fetch_one('app_fields', [
            'id = ?',
            \K::$fw->GET['fields_id']
        ], [], 'name,configuration');

        if (!\K::$fw->fields_info) {
            \Helpers\Urls::redirect_to('main/entities/fields', 'entities_id=' . \K::$fw->GET['entities_id']);
        }

        $cfg = new \Models\Main\Fields_types_cfg(\K::$fw->fields_info['configuration']);

        $entity_id = $cfg->get('entity_id');

        $reports_type = 'entityfield' . (int)\K::$fw->GET['fields_id'];
        /*$reports_info_query = db_query(
            "select * from app_reports where entities_id='" . db_input(
                $entity_id
            ) . "' and reports_type='{$reports_type}'"
        );*/

        \K::$fw->reports_info = \K::model()->db_fetch_one('app_reports', [
            'entities_id = ? and reports_type = ?',
            $entity_id,
            $reports_type
        ], [], 'id');

        if (!\K::$fw->reports_info) {
            $sql_data = [
                'name' => '',
                'entities_id' => $entity_id,
                'reports_type' => $reports_type,
                'in_menu' => 0,
                'in_dashboard' => 0,
                'created_by' => 0,
            ];

            \K::model()->db_perform('app_reports', $sql_data);

            \Helpers\Urls::redirect_to(
                'main/entities/entityfield_filters',
                'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
            );
        }

        \K::$fw->countReportsFilters = \K::model()->db_count(
            'app_reports_filters',
            \K::$fw->reports_info['id'],
            'reports_id'
        );

        \K::$fw->filters_query = \K::model()->db_query_exec(
            "select rf.*, f.name, f.type from app_reports_filters rf left join app_fields f on rf.fields_id = f.id where rf.reports_id = ? order by rf.id",
            \K::$fw->reports_info['id'],
            'app_reports_filters,app_fields'
        );

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'entityfield_filters.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        $values = '';

        if (isset($_POST['values'])) {
            if (is_array($_POST['values'])) {
                $values = implode(',', $_POST['values']);
            } else {
                $values = $_POST['values'];
            }
        }
        $sql_data = [
            'reports_id' => \K::$fw->GET['reports_id'],
            'fields_id' => $_POST['fields_id'],
            'filters_condition' => $_POST['filters_condition'],
            'filters_values' => $values,
        ];

        if (isset(\K::$fw->GET['id'])) {
            db_perform('app_reports_filters', $sql_data, 'update', "id='" . db_input(\K::$fw->GET['id']) . "'");
        } else {
            db_perform('app_reports_filters', $sql_data);
        }

        redirect_to(
            'entities/entityfield_filters',
            'reports_id=' . \K::$fw->GET['reports_id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
        );
    }

    public function delete()
    {
        if (isset(\K::$fw->GET['id'])) {
            db_query("delete from app_reports_filters where id='" . db_input(\K::$fw->GET['id']) . "'");

            $alerts->add(TEXT_WARN_DELETE_FILTER_SUCCESS, 'success');

            redirect_to(
                'entities/entityfield_filters',
                'reports_id=' . \K::$fw->GET['reports_id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
            );
        }
    }
}