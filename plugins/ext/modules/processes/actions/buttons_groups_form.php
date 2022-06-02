<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_processes_buttons_groups', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_processes_buttons_groups');

    $obj['entities_id'] = $processes_filter;
}
