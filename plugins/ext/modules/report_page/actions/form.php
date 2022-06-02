<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_report_page', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_report_page');

    if ($report_page_filter > 0) {
        $obj['entities_id'] = $report_page_filter;
    }

    $obj['is_active'] = 0;
    $obj['type'] = 'print';
}