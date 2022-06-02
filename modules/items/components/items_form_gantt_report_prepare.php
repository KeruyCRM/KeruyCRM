<?php

$reports_query = db_query(
    "select * from app_ext_ganttchart where id='" . str_replace('ganttreport', '', $app_redirect_to) . "'"
);
if ($reports = db_fetch_array($reports_query)) {
    $start_date_timestamp = ($_GET['start']) / 1000;
    $end_date_timestamp = ($_GET['end']) / 1000;

    if (ganttchart::get_duration_unit($reports) == 'hour') {
        $obj['field_' . $reports['start_date']] = $start_date_timestamp;
        $obj['field_' . $reports['end_date']] = strtotime('-1 hour', $end_date_timestamp);
    } else {
        $obj['field_' . $reports['start_date']] = $start_date_timestamp;
        $obj['field_' . $reports['end_date']] = strtotime('-1 day', $end_date_timestamp);
    }

    $field = $app_fields_cache[$reports['entities_id']][$reports['end_date']];
    $cfg = new fields_types_cfg($field['configuration']);
    if (strlen($cfg->get('default_value')) > 0) {
        $obj['field_' . $reports['end_date']] = strtotime("+" . (int)$cfg->get('default_value') . " day");
    }
}