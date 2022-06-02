<?php

$reports_query = db_query("select * from app_ext_image_map order by name");
while ($reports = db_fetch_array($reports_query)) {
    if (image_map::has_access($reports['users_groups'])) {
        $check_query = db_query(
            "select id from app_entities_menu where find_in_set('image_map" . $reports['id'] . "',reports_list)"
        );
        if (!$check = db_fetch_array($check_query)) {
            if ($reports['in_menu']) {
                $app_plugin_menu['menu'][] = [
                    'title' => $reports['name'],
                    'url' => url_for('ext/image_map/view', 'id=' . $reports['id']),
                    'class' => 'fa-picture-o'
                ];
            } else {
                $app_plugin_menu['reports'][] = [
                    'title' => $reports['name'],
                    'url' => url_for('ext/image_map/view', 'id=' . $reports['id'])
                ];
            }
        }
    }
}