<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_pivot_calendars', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_pivot_calendars');

    $obj['default_view'] = 'month';
    $obj['time_slot_duration'] = '00:30:00';
    $obj['event_limit'] = 6;
}