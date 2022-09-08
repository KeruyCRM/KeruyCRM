<?php

if (!defined('KERUY_CRM')) {
    exit;
}

/*$reports_query = db_query(
    "select * from app_ext_ganttchart where id='" . str_replace('ganttreport', '', \K::$fw->app_redirect_to) . "'"
);*/

$reports = \K::model()->db_fetch_one('app_ext_ganttchart', [
    'id = ?',
    str_replace('ganttreport', '', \K::$fw->app_redirect_to)
]);

if ($reports) {
    $start_date_timestamp = (\K::$fw->GET['start']) / 1000;
    $end_date_timestamp = (\K::$fw->GET['end']) / 1000;

    \K::$fw->obj['field_' . $reports['start_date']] = $start_date_timestamp;

    if (ganttchart::get_duration_unit($reports) == 'hour') {
        \K::$fw->obj['field_' . $reports['end_date']] = strtotime('-1 hour', $end_date_timestamp);
    } else {
        \K::$fw->obj['field_' . $reports['end_date']] = strtotime('-1 day', $end_date_timestamp);
    }

    $field = \K::$fw->app_fields_cache[$reports['entities_id']][$reports['end_date']];
    $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);
    if (strlen($cfg->get('default_value')) > 0) {
        \K::$fw->obj['field_' . $reports['end_date']] = strtotime("+" . (int)$cfg->get('default_value') . " day");
    }
}