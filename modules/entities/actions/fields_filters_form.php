<?php

$fields_info_query = db_query("select * from app_fields where id='" . $_GET['fields_id'] . "'");
if (!$fields_info = db_fetch_array($fields_info_query)) {
    redirect_to('entities/fields', 'entities_id=' . $_GET['entities_id']);
}

switch ($fields_info['type']) {
    case 'fieldtype_related_records':
        $reports_type = 'related_items_' . $_GET['fields_id'];
        break;
    default:
        $reports_type = 'fieldfilter' . $_GET['fields_id'];
        break;
}

$reports_info_query = db_query(
    "select * from app_reports where id='" . db_input(
        $_GET['reports_id']
    ) . "' and reports_type='" . $reports_type . "'"
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