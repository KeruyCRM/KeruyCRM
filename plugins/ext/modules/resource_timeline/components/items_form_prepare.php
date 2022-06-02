<?php

$calendar_reports_query = db_query(
    "select * from app_ext_resource_timeline_entities where id='" . db_input(
        str_replace('resource_timeline', '', $app_redirect_to)
    ) . "'"
);
if ($calendar_reports = db_fetch_array($calendar_reports_query)) {
    $start_date_timestamp = ($_GET['start']) / 1000;
    $end_date_timestamp = ($_GET['end']) / 1000;

    $offset = date('Z');

    if ($offset < 0) {
        $start_date_timestamp += abs($offset);
        $end_date_timestamp += abs($offset);
    } else {
        $start_date_timestamp -= abs($offset);
        $end_date_timestamp -= abs($offset);
    }

    if ($_GET['view_name'] == 'timelineMonth') {
        $obj['field_' . $calendar_reports['start_date']] = $start_date_timestamp;
        $obj['field_' . $calendar_reports['end_date']] = strtotime('-1 day', $end_date_timestamp);
    } else {
        $obj['field_' . $calendar_reports['start_date']] = $start_date_timestamp;
        $obj['field_' . $calendar_reports['end_date']] = $end_date_timestamp;
    }

    if ($calendar_reports['related_entity_field_id'] > 0 and isset($_GET['resource_id'])) {
        $obj['field_' . $calendar_reports['related_entity_field_id']] = _GET('resource_id');
    }
}

//print_rr($obj);

