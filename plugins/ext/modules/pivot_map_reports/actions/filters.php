<?php

$pivot_map_info_query = db_query(
    "select * from app_ext_pivot_map_reports where id='" . _get::int('map_reports_id') . "'"
);
if (!$pivot_map_info = db_fetch_array($pivot_map_info_query)) {
    redirect_to('ext/pivot_calendars/reports');
}

$current_reports_info_query = db_query("select * from app_reports where id='" . _get::int('reports_id') . "'");
if (!$current_reports_info = db_fetch_array($current_reports_info_query)) {
    $alerts->add(TEXT_REPORT_NOT_FOUND, 'error');
    redirect_to('ext/pivot_calendars/reports');
}

switch ($app_module_action) {
    case 'save':

        $values = '';

        if (isset($_POST['values'])) {
            if (is_array($_POST['values'])) {
                $values = implode(',', $_POST['values']);
            } else {
                $values = $_POST['values'];
            }
        }
        $sql_data = [
            'reports_id' => (isset($_GET['parent_reports_id']) ? $_GET['parent_reports_id'] : $_GET['reports_id']),
            'fields_id' => $_POST['fields_id'],
            'filters_condition' => isset($_POST['filters_condition']) ? $_POST['filters_condition'] : '',
            'filters_values' => $values,
            'is_active' => $_POST['is_active'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_reports_filters', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
            $filters_id = $_GET['id'];
        } else {
            db_perform('app_reports_filters', $sql_data);
            $filters_id = db_insert_id();
        }


        redirect_to(
            'ext/pivot_map_reports/filters',
            'map_reports_id=' . $pivot_map_info['id'] . '&reports_id=' . $_GET['reports_id']
        );

        break;
    case 'delete':
        if (isset($_GET['id'])) {
            if ($_GET['id'] == 'all') {
                db_query(
                    "delete from app_reports_filters where reports_id='" . db_input(
                        (isset($_GET['parent_reports_id']) ? $_GET['parent_reports_id'] : $_GET['reports_id'])
                    ) . "'"
                );
                $alerts->add(TEXT_WARN_DELETE_ALL_FILTERS_SUCCESS, 'success');
            } else {
                db_query("delete from app_reports_filters where id='" . db_input($_GET['id']) . "'");
                //$alerts->add(TEXT_WARN_DELETE_FILTER_SUCCESS,'success');
            }
        }

        redirect_to(
            'ext/pivot_map_reports/filters',
            'map_reports_id=' . $pivot_map_info['id'] . '&reports_id=' . $_GET['reports_id']
        );
        break;
}


