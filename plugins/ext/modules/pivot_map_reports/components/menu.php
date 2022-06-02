<?php

$reports_query = db_query("select * from app_ext_pivot_map_reports order by name");
while ($reports = db_fetch_array($reports_query)) {
    if (pivot_map_reports::has_access($reports['users_groups'])) {
        $check_query = db_query(
            "select id from app_entities_menu where find_in_set('pivot_map_reports" . $reports['id'] . "',reports_list)"
        );
        if (!$check = db_fetch_array($check_query)) {
            if ($reports['in_menu']) {
                $app_plugin_menu['menu'][] = [
                    'title' => $reports['name'],
                    'url' => url_for('ext/pivot_map_reports/view', 'id=' . $reports['id']),
                    'class' => 'fa-map-marker'
                ];
            } else {
                $app_plugin_menu['reports'][] = [
                    'title' => $reports['name'],
                    'url' => url_for('ext/pivot_map_reports/view', 'id=' . $reports['id'])
                ];
            }
        }
    }
}
