<?php

switch ($app_module_action) {
    case 'save':

        //check min/max dates
        $min_time = $_POST['min_time'];
        $max_time = $_POST['max_time'];

        if ((int)$min_time > (int)$max_time) {
            $max_time = '';
        }

        if (!strstr($min_time, ':00') and !strstr($min_time, ':30') and strlen($min_time)) {
            $min_time = explode(':', $min_time);
            $min_time = $min_time[0] . ':00';
        }

        if (!strstr($max_time, ':00') and !strstr($max_time, ':30') and strlen($max_time)) {
            $max_time = explode(':', $max_time);
            $max_time = $max_time[0] . ':00';
        }

        $sql_data = [
            'name' => $_POST['name'],
            'default_view' => $_POST['default_view'],
            'enable_ical' => $_POST['enable_ical'],
            'view_modes' => (isset($_POST['view_modes']) ? implode(',', $_POST['view_modes']) : ''),
            'event_limit' => $_POST['event_limit'],
            'highlighting_weekends' => (isset($_POST['highlighting_weekends']) ? implode(
                ',',
                $_POST['highlighting_weekends']
            ) : ''),
            'min_time' => $min_time,
            'max_time' => $max_time,
            'time_slot_duration' => $_POST['time_slot_duration'],
            'display_legend' => (isset($_POST['display_legend']) ? $_POST['display_legend'] : 0),
            'in_menu' => (isset($_POST['in_menu']) ? $_POST['in_menu'] : 0),
            'users_groups' => (isset($_POST['access']) ? json_encode($_POST['access']) : ''),
            'sort_order' => $_POST['sort_order'],
        ];


        if (isset($_GET['id'])) {
            $calendar_id = $_GET['id'];

            db_perform('app_ext_pivot_calendars', $sql_data, 'update', "id='" . db_input($calendar_id) . "'");
        } else {
            db_perform('app_ext_pivot_calendars', $sql_data);
            $calendar_id = db_insert_id();
        }


        redirect_to('ext/pivot_calendars/reports');
        break;

    case 'delete':
        $calendar_id = _get::int('id');

        $obj = db_find('app_ext_pivot_calendars', $calendar_id);

        db_delete_row('app_ext_pivot_calendars', $calendar_id);

        $entities_query = db_query(
            "select id from app_ext_pivot_calendars_entities where calendars_id='" . $calendar_id . "'"
        );
        while ($entities = db_fetch_array($entities_query)) {
            $report_info_query = db_query(
                "select * from app_reports where reports_type='pivot_calendars" . $entities['id'] . "'"
            );
            if ($report_info = db_fetch_array($report_info_query)) {
                reports::delete_reports_by_id($report_info['id']);
            }
        }

        db_delete_row('app_ext_pivot_calendars_entities', $calendar_id, 'calendars_id');

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('ext/pivot_calendars/reports');
}