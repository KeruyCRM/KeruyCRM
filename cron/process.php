<?php

chdir(substr(__DIR__, 0, -5));

define('IS_CRON', true);

//load core
require('includes/application_core.php');

//include ext plugins
require('plugins/ext/application_core.php');

//load app lagn
if (is_file($v = 'includes/languages/' . CFG_APP_LANGUAGE)) {
    require($v);
}

if (is_file($v = 'plugins/ext/languages/' . CFG_APP_LANGUAGE)) {
    require($v);
}

$app_users_cache = users::get_cache();

$app_user = [
    'id' => 0,
    'group_id' => 0,
    'name' => CFG_EMAIL_NAME_FROM,
    'email' => CFG_EMAIL_ADDRESS_FROM,
];

$process_id = (isset($_GET['process_id']) ? _GET('process_id') : (isset($argv[1]) ? (int)$argv[1] : false));
$item_id = (isset($_GET['item_id']) ? _GET('item_id') : (isset($argv[2]) ? (int)$argv[2] : false));

//check process id
if (!$process_id) {
    die("Error: process_id is not passed!");
}

//find process
$process_query = db_query(
    "select * from app_ext_processes where find_in_set('run_on_schedule',button_position) and  id={$process_id}"
);
if (!$process = db_fetch_array($process_query)) {
    die("Error: process #{$process_id} is not found!");
}

//check status
if ($process['is_active'] == 0) {
    die();
}

//check process fielter
$reports_info_query = db_query(
    "select * from app_reports where entities_id='" . db_input(
        $process['entities_id']
    ) . "' and reports_type='process" . $process['id'] . "'"
);
if ($reports_info = db_fetch_array($reports_info_query)) {
    if (!reports::count_filters_by_reports_id($reports_info['id']) and !$item_id) {
        die("Error: filters are not setup for process #{$process_id}!");
    }
}

$processes = new processes($process['entities_id']);

//run process for single item
if ($item_id) {
    //check if item eixt
    $check_qeury = db_query("select id from app_entity_{$process['entities_id']} where id={$item_id}");
    if (!$check = db_fetch_array($check_qeury)) {
        die("Error: item #{$item_id} not found");
    }

    $processes->items_id = $item_id;
    if ($processes->check_buttons_filters($process)) {
        $processes->run($process, false, true);
    }
} //run process for filtered items
elseif ($reports_info) {
    $listing_sql_query = '';
    $listing_sql_query_select = '';
    $listing_sql_query_having = '';
    $sql_query_having = [];

    //prepare forumulas query
    $listing_sql_query_select = fieldtype_formula::prepare_query_select($process['entities_id']);

    $listing_sql_query = reports::add_filters_query($reports_info['id'], $listing_sql_query);

    //prepare having query for formula fields
    if (isset($sql_query_having[$process['entities_id']])) {
        $listing_sql_query_having = reports::prepare_filters_having_query($sql_query_having[$process['entities_id']]);
    }

    $listing_sql_query .= $listing_sql_query_having;

    $item_sql = "select e.* " . $listing_sql_query_select . " from app_entity_" . $process['entities_id'] . " e  where e.id>0 " . $listing_sql_query;

    $item_query = db_query($item_sql);
    while ($item = db_fetch_array($item_query)) {
        //print_rr($item);

        $processes->items_id = $item['id'];
        $processes->run($process, false, true);
    }
}
