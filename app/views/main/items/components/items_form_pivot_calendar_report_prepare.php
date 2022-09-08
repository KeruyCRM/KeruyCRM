<?php

if (!defined('KERUY_CRM')) {
    exit;
}

/*$calendar_reports_id = str_replace('pivot_calendars', '', $app_redirect_to);
$calendar_reports_query = db_query(
    "select * from app_ext_pivot_calendars_entities where id='" . db_input($calendar_reports_id) . "'"
);*/

$calendar_reports = \K::model()->db_fetch_one('app_ext_pivot_calendars_entities', [
    'id = ?',
    str_replace('pivot_calendars', '', \K::$fw->app_redirect_to)
]);

if ($calendar_reports) {
    $start_date_timestamp = (\K::$fw->GET['start']) / 1000;
    $end_date_timestamp = (\K::$fw->GET['end']) / 1000;

    $offset = date('Z');

    if ($offset < 0) {
        $start_date_timestamp += abs($offset);
        $end_date_timestamp += abs($offset);
    } else {
        $start_date_timestamp -= abs($offset);
        $end_date_timestamp -= abs($offset);
    }

    \K::$fw->obj['field_' . $calendar_reports['start_date']] = $start_date_timestamp;

    if (\K::$fw->GET['view_name'] == 'month') {
        \K::$fw->obj['field_' . $calendar_reports['end_date']] = strtotime('-1 day', $end_date_timestamp);
    } else {
        \K::$fw->obj['field_' . $calendar_reports['end_date']] = $end_date_timestamp;
    }
}