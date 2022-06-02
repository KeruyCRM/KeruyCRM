<?php

$reports_info_query = db_query(
    "select * from app_reports where id='" . db_input(
        (isset($_GET['parent_reports_id']) ? $_GET['parent_reports_id'] : $_GET['reports_id'])
    ) . "'"
);
if (!$reports_info = db_fetch_array($reports_info_query)) {
    $alerts->add(TEXT_REPORT_NOT_FOUND, 'error');
    redirect_to('ext/xml_export/templates');
}

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_reports_filters', $_GET['id']);
} else {
    $obj = db_show_columns('app_reports_filters');
}