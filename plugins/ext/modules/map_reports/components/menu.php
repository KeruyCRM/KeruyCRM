<?php

$reports_query = db_query("select * from app_ext_map_reports order by name");
while ($reports = db_fetch_array($reports_query)) {
    if (map_reports::has_access($reports['users_groups'])) {
        $check_query = db_query(
            "select id from app_entities_menu where find_in_set('map_reports" . $reports['id'] . "',reports_list)"
        );
        if (!$check = db_fetch_array($check_query)) {
            if ($reports['in_menu']) {
                $app_plugin_menu['menu'][] = [
                    'title' => $reports['name'],
                    'url' => url_for('ext/map_reports/view', 'id=' . $reports['id']),
                    'class' => 'fa-map-marker'
                ];
            } else {
                $app_plugin_menu['reports'][] = [
                    'title' => $reports['name'],
                    'url' => url_for('ext/map_reports/view', 'id=' . $reports['id'])
                ];
            }
        }
    }
}


/**
 *add map reports to items menu
 */
if (isset($_GET['path'])) {
    $entities_list = items::get_sub_entities_list_by_path($_GET['path']);

    if (count($entities_list)) {
        $reports_query = db_query(
            "select g.* from app_ext_map_reports g, app_entities e  where e.id=g.entities_id and e.id in (" . implode(
                ',',
                $entities_list
            ) . ") " . ($app_user['group_id'] > 0 ? " and find_in_set(" . $app_user['group_id'] . ",g.users_groups)" : "") . " order by g.name"
        );

        while ($reports = db_fetch_array($reports_query)) {
            $path = app_get_path_to_report($reports['entities_id']);

            $app_plugin_menu['items_menu_reports'][] = [
                'title' => $reports['name'],
                'url' => url_for('ext/map_reports/view', 'id=' . $reports['id'] . '&path=' . $path)
            ];
        }
    }
}