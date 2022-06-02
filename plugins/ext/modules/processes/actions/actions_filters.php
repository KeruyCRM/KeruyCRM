<?php

$app_process_info_query = db_query("select * from app_ext_processes where id='" . _get::int('process_id') . "'");
if (!$app_process_info = db_fetch_array($app_process_info_query)) {
    redirect_to('ext/processes/processes');
}

$app_actions_info_query = db_query(
    "select * from app_ext_processes_actions where process_id='" . _get::int('process_id') . "' and id='" . _get::int(
        'actions_id'
    ) . "'"
);
if (!$app_actions_info = db_fetch_array($app_actions_info_query)) {
    redirect_to('ext/processes/processes');
}

$action_entity_id = processes::get_entity_id_from_action_type($app_actions_info['type']);

$reports_info_query = db_query(
    "select * from app_reports where entities_id='" . db_input(
        $action_entity_id
    ) . "' and reports_type='process_action" . $app_actions_info['id'] . "'"
);
if (!$reports_info = db_fetch_array($reports_info_query)) {
    $sql_data = [
        'name' => '',
        'entities_id' => $action_entity_id,
        'reports_type' => 'process_action' . $app_actions_info['id'],
        'in_menu' => 0,
        'in_dashboard' => 0,
        'created_by' => 0,
    ];
    db_perform('app_reports', $sql_data);

    redirect_to(
        'ext/processes/actions_filters',
        'process_id=' . _get::int('process_id') . '&actions_id=' . _get::int('actions_id')
    );
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

        redirect_to(
            'ext/processes/actions_filters',
            'process_id=' . $_GET['process_id'] . '&actions_id=' . _get::int('actions_id')
        );
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            db_query("delete from app_reports_filters where id='" . db_input($_GET['id']) . "'");

            $alerts->add(TEXT_WARN_DELETE_FILTER_SUCCESS, 'success');

            redirect_to(
                'ext/processes/actions_filters',
                'process_id=' . $_GET['process_id'] . '&actions_id=' . _get::int('actions_id')
            );
        }
        break;
}
