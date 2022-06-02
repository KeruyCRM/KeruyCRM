<?php

$app_process_info_query = db_query("select * from app_ext_processes where id='" . _get::int('process_id') . "'");
if (!$app_process_info = db_fetch_array($app_process_info_query)) {
    redirect_to('ext/processes/processes');
}

$reports_info_query = db_query(
    "select * from app_reports where entities_id='" . db_input(
        $app_process_info['entities_id']
    ) . "' and reports_type='process" . $app_process_info['id'] . "'"
);
if (!$reports_info = db_fetch_array($reports_info_query)) {
    $sql_data = [
        'name' => '',
        'entities_id' => $app_process_info['entities_id'],
        'reports_type' => 'process' . $app_process_info['id'],
        'in_menu' => 0,
        'in_dashboard' => 0,
        'created_by' => 0,
    ];
    db_perform('app_reports', $sql_data);

    redirect_to('ext/processes/filters', 'process_id=' . _get::int('process_id'));
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
            'reports_id' => $reports_info['id'],
            'fields_id' => $_POST['fields_id'],
            'filters_condition' => $_POST['filters_condition'],
            'filters_values' => $values,
        ];

        if (isset($_GET['id'])) {
            db_perform('app_reports_filters', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_reports_filters', $sql_data);
        }

        redirect_to('ext/processes/filters', 'process_id=' . $_GET['process_id']);
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            db_query("delete from app_reports_filters where id='" . db_input($_GET['id']) . "'");

            $alerts->add(TEXT_WARN_DELETE_FILTER_SUCCESS, 'success');

            redirect_to('ext/processes/filters', 'process_id=' . $_GET['process_id']);
        }
        break;
}
