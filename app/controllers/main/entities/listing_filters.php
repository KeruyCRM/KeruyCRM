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

        $reports_info_query = db_query(
            "select * from app_reports where entities_id='" . db_input(
                $_GET['entities_id']
            ) . "' and reports_type='default'"
        );
        if (!$reports_info = db_fetch_array($reports_info_query)) {
            $sql_data = [
                'name' => '',
                'entities_id' => $_GET['entities_id'],
                'reports_type' => 'default',
                'in_menu' => 0,
                'in_dashboard' => 0,
                'created_by' => 0,
            ];
            db_perform('app_reports', $sql_data);

            redirect_to('entities/listing_filters', 'entities_id=' . $_GET['entities_id']);
        }
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'listing_filters.php';

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
            'filters_condition' => ($_POST['filters_condition'] ?? ''),
            'filters_values' => $values,
        ];

        if (isset($_GET['id'])) {
            db_perform('app_reports_filters', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_reports_filters', $sql_data);
        }

        redirect_to(
            'entities/listing_filters',
            'reports_id=' . $_GET['reports_id'] . '&entities_id=' . $_GET['entities_id']
        );
    }

    public function delete()
    {
        if (isset($_GET['id'])) {
            db_query("delete from app_reports_filters where id='" . db_input($_GET['id']) . "'");

            $alerts->add(TEXT_WARN_DELETE_FILTER_SUCCESS, 'success');

            redirect_to(
                'entities/listing_filters',
                'reports_id=' . $_GET['reports_id'] . '&entities_id=' . $_GET['entities_id']
            );
        }
    }
}