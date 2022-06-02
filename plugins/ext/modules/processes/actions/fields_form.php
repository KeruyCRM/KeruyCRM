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

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_processes_actions_fields', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_processes_actions_fields');
}