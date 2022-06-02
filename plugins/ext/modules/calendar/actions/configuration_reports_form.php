<?php

//check access
if ($app_user['group_id'] > 0) {
    exit();
}

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_calendar', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_calendar');
    $obj['default_view'] = 'month';
    $obj['time_slot_duration'] = '00:30:00';
    $obj['event_limit'] = 6;
    $obj['filters_panel'] = 'default';
}