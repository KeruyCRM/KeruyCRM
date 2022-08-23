<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Hide_subentity_filters extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();

        $entities_info = \K::model()->db_find('app_entities', \K::$fw->GET['entities_id']);

        $reports_type = 'hide_subentity_' . (int)\K::$fw->GET['entities_id'];

        /*$reports_info_query = db_query(
            "select * from app_reports where entities_id='" . db_input(
                $entities_info['parent_id']
            ) . "' and reports_type='{$reports_type}'"
        );*/

        $reports_info = \K::model()->db_fetch_one('app_reports', [
            'entities_id = ? and reports_type = ?',
            $entities_info['parent_id'],
            $reports_type
        ]);

        if (!$reports_info) {
            $sql_data = [
                'name' => '',
                'entities_id' => $entities_info['parent_id'],
                'reports_type' => $reports_type,
                'in_menu' => 0,
                'in_dashboard' => 0,
                'created_by' => 0,
            ];

            $mapper = \K::model()->db_perform('app_reports', $sql_data);
            $id = \K::model()->db_insert_id($mapper);

            $reports_info = \K::model()->db_find('app_reports', $id);
        }
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'hide_subentity_filters.php';

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
            'reports_id' => $_GET['reports_id'],
            'fields_id' => $_POST['fields_id'],
            'filters_condition' => $_POST['filters_condition'],
            'filters_values' => $values,
        ];

        if (isset($_GET['id'])) {
            db_perform('app_reports_filters', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_reports_filters', $sql_data);
        }

        redirect_to(
            'entities/hide_subentity_filters',
            'reports_id=' . $_GET['reports_id'] . '&entities_id=' . $_GET['entities_id']
        );
    }

    public function delete()
    {
        if (isset($_GET['id'])) {
            db_query("delete from app_reports_filters where id='" . db_input($_GET['id']) . "'");

            $alerts->add(TEXT_WARN_DELETE_FILTER_SUCCESS, 'success');

            redirect_to(
                'entities/hide_subentity_filters',
                'reports_id=' . $_GET['reports_id'] . '&entities_id=' . $_GET['entities_id']
            );
        }
    }
}