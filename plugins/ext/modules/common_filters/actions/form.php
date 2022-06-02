<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_reports', $_GET['id']);
} else {
    $obj = db_show_columns('app_reports');

    $obj['entities_id'] = $common_filters_filter;
}