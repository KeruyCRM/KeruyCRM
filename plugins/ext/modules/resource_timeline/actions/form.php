<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_resource_timeline', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_resource_timeline');

    $obj['time_slot_duration'] = '00:30:00';
    $obj['default_view'] = 'timelineMonth';
}