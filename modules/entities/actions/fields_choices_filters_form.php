<?php

$reports_info_query = db_query(
    "select * from app_reports where id='" . db_input(
        $_GET['reports_id']
    ) . "' and reports_type='fields_choices" . $_GET['choices_id'] . "'"
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