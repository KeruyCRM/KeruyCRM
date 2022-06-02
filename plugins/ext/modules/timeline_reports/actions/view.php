<?php

//check if report exist
$reports_query = db_query("select * from app_ext_timeline_reports where id='" . db_input((int)$_GET['id']) . "'");
if (!$reports = db_fetch_array($reports_query)) {
    redirect_to('dashboard/page_not_found');
}

if (!in_array($app_user['group_id'], explode(',', $reports['allowed_groups'])) and $app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}

//create default entity report for logged user
$reports_info_query = db_query(
    "select * from app_reports where entities_id='" . db_input(
        $reports['entities_id']
    ) . "' and reports_type='timelinereport" . $reports['id'] . "' and created_by='" . $app_logged_users_id . "'"
);
if (!$reports_info = db_fetch_array($reports_info_query)) {
    $sql_data = [
        'name' => '',
        'entities_id' => $reports['entities_id'],
        'reports_type' => 'timelinereport' . $reports['id'],
        'in_menu' => 0,
        'in_dashboard' => 0,
        'listing_order_fields' => '',
        'created_by' => $app_logged_users_id,
    ];

    db_perform('app_reports', $sql_data);
    $fiters_reports_id = db_insert_id();
} else {
    $fiters_reports_id = $reports_info['id'];
}

$entity_info = db_find('app_entities', $reports['entities_id']);

//check if parent reports was not set
if ($entity_info['parent_id'] > 0 and $reports_info['parent_id'] == 0) {
    reports::auto_create_parent_reports($reports_info['id']);
}


$heading_field_id = fields::get_heading_id($reports['entities_id']);

if (!$heading_field_id) {
    $alerts->add(TEXT_ERROR_NO_HEADING_FIELD, 'warning');
}

$app_title = $reports['name'];

