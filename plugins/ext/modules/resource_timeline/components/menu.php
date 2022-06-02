<?php


$reports_query = db_query("select * from app_ext_resource_timeline order by sort_order, name");
while ($reports = db_fetch_array($reports_query)) {
    if (resource_timeline::has_access($reports['users_groups'])) {
        $check_query = db_query(
            "select id from app_entities_menu where find_in_set('resource_timeline" . $reports['id'] . "',reports_list)"
        );
        if (!$check = db_fetch_array($check_query)) {
            if ($reports['in_menu'] == 1) {
                $app_plugin_menu['menu'][] = [
                    'title' => $reports['name'],
                    'url' => url_for('ext/resource_timeline/view', 'id=' . $reports['id']),
                    'class' => 'fa-calendar'
                ];
            } else {
                $app_plugin_menu['reports'][] = [
                    'title' => $reports['name'],
                    'url' => url_for('ext/resource_timeline/view', 'id=' . $reports['id'])
                ];
            }
        }
    }
}