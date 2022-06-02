<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_processes', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_processes');

    $obj['entities_id'] = $processes_filter;
    $obj['is_active'] = true;
    $obj['is_form_wizard'] = 0;
}