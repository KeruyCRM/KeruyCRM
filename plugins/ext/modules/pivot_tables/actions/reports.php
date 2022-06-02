<?php

if (!app_session_is_registered('pivot_reports_filter')) {
    $pivot_reports_filter = 0;
    app_session_register('pivot_reports_filter');
}

switch ($app_module_action) {
    case 'set_reports_filter':
        $pivot_reports_filter = $_POST['reports_filter'];

        redirect_to('ext/pivot_tables/reports');
        break;
    case 'save':

        $sql_data = [
            'name' => db_prepare_input($_POST['name']),
            'entities_id' => $_POST['entities_id'],
            'in_menu' => (isset($_POST['in_menu']) ? $_POST['in_menu'] : 0),
            'users_groups' => (isset($_POST['access']) ? json_encode($_POST['access']) : ''),
            'sort_order' => $_POST['sort_order'],
            'chart_type' => $_POST['chart_type'],
            'chart_position' => $_POST['chart_position'],
            'filters_panel' => $_POST['filters_panel'],
            'height' => $_POST['height'],
            'chart_height' => $_POST['chart_height'],
            'colors' => implode(',', $_POST['colors']),
        ];


        if (isset($_GET['id'])) {
            //check if entity changed
            $pivotreports = db_find('app_ext_pivot_tables', $_GET['id']);
            if ($pivotreports['entities_id'] != $_POST['entities_id']) {
                db_delete_row('app_ext_pivot_tables_fields', $_GET['id'], 'reports_id');
                db_delete_row('app_ext_pivot_tables_settings', $_GET['id'], 'reports_id');
            }

            db_perform('app_ext_pivot_tables', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_pivot_tables', $sql_data);
        }

        redirect_to('ext/pivot_tables/reports');

        break;
    case 'delete':
        $obj = db_find('app_ext_pivot_tables', $_GET['id']);

        db_delete_row('app_ext_pivot_tables', $_GET['id']);
        db_delete_row('app_ext_pivot_tables_fields', $_GET['id'], 'reports_id');
        db_delete_row('app_ext_pivot_tables_settings', $_GET['id'], 'reports_id');

        $report_info_query = db_query(
            "select * from app_reports where reports_type='pivot_tables" . db_input(
                $_GET['id']
            ) . "' or reports_type='default_pivot_tables" . db_input($_GET['id']) . "'"
        );
        if ($report_info = db_fetch_array($report_info_query)) {
            reports::delete_reports_by_id($report_info['id']);
        }

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('ext/pivot_tables/reports');
        break;
}