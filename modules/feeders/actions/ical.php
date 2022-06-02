<?php

if (isset($_GET['download'])) {
    $filename = 'calendar-' . $_GET['type'] . (isset($_GET['id']) ? '-' . (int)$_GET['id'] : '') . '.ics';
    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
} else {
    header('Content-type: text/plain; charset=utf-8');
}


header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


require_once("includes/libs/icalendar-master/zapcallib.php");

$client_id = _GET('client');
$type = $_GET['type'];
$reports_id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

//check if user exist by client ID and user is active
$user_query = db_query("select * from app_entity_1 where client_id='{$client_id}' and field_5=1");
if (!$user = db_fetch_array($user_query)) {
    die(TEXT_NO_ACCESS);
} else {
    $app_user = [
        'id' => $user['id'],
        'group_id' => (int)$user['field_6']
    ];
}


$icalendar = new icalendar($type, $reports_id);

$icalendar->export();

app_exit();