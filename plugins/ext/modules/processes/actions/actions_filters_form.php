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

$reports_info_query = db_query(
    "select * from app_reports where reports_type='process_action" . $app_actions_info['id'] . "'"
);
if (!$reports_info = db_fetch_array($reports_info_query)) {
    echo TEXT_REPORT_NOT_FOUND;
    exit();
}

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_reports_filters', $_GET['id']);
} else {
    $obj = db_show_columns('app_reports_filters');
}