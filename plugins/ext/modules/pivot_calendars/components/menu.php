<?php

/**
 *add pivot calendar reports to menu
 */
$reports_query = db_query("select * from app_ext_pivot_calendars order by sort_order, name");
while ($reports = db_fetch_array($reports_query)) {
    if (pivot_calendars::has_access($reports['users_groups'])) {
        $check_query = db_query(
            "select id from app_entities_menu where find_in_set('pivot_calendars" . $reports['id'] . "',reports_list)"
        );
        if (!$check = db_fetch_array($check_query)) {
            if ($reports['in_menu'] == 1) {
                $app_plugin_menu['menu'][] = [
                    'title' => $reports['name'],
                    'url' => url_for('ext/pivot_calendars/view', 'id=' . $reports['id']),
                    'class' => 'fa-calendar'
                ];
            } else {
                $app_plugin_menu['reports'][] = [
                    'title' => $reports['name'],
                    'url' => url_for('ext/pivot_calendars/view', 'id=' . $reports['id'])
                ];
            }
        }
    }
}