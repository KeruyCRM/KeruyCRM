<?php

$app_process_info_query = db_query("select * from app_ext_processes where id='" . _get::int('process_id') . "'");
if (!$app_process_info = db_fetch_array($app_process_info_query)) {
    redirect_to('ext/processes/processes');
}

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_processes_actions', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_processes_actions');

    $actions_query = db_query(
        "select max(sort_order) as max_sort_order from app_ext_processes_actions where process_id='" . $app_process_info['id'] . "'"
    );
    $actions = db_fetch_array($actions_query);

    $obj['sort_order'] = (int)$actions['max_sort_order'] + 1;
    $obj['is_active'] = 1;
}