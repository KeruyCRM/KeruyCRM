<?php

$reports_query = db_query("select * from app_ext_mind_map order by name");
while ($reports = db_fetch_array($reports_query)) {
    if (mind_map_reports::has_access($reports['users_groups'])) {
        $check_query = db_query(
            "select id from app_entities_menu where find_in_set('mind_map" . $reports['id'] . "',reports_list)"
        );
        if (!$check = db_fetch_array($check_query)) {
            if ($reports['in_menu']) {
                $app_plugin_menu['menu'][] = [
                    'title' => $reports['name'],
                    'url' => url_for('ext/mind_map_reports/view', 'id=' . $reports['id']),
                    'class' => 'fa-sitemap'
                ];
            } elseif ($app_entities_cache[$reports['entities_id']]['parent_id'] == 0) {
                $app_plugin_menu['reports'][] = [
                    'title' => $reports['name'],
                    'url' => url_for('ext/mind_map_reports/view', 'id=' . $reports['id'])
                ];
            }
        }
    }
}


if (isset($_GET['path'])) {
    $entities_list = items::get_sub_entities_list_by_path($_GET['path']);

    if (count($entities_list)) {
        $reports_query = db_query(
            "select mm.* from app_ext_mind_map mm, app_entities e where e.id=mm.entities_id and  e.id in (" . implode(
                ',',
                $entities_list
            ) . ")  order by mm.name"
        );

        while ($reports = db_fetch_array($reports_query)) {
            if (mind_map_reports::has_access($reports['users_groups'])) {
                $path = app_get_path_to_report($reports['entities_id']);

                $app_plugin_menu['items_menu_reports'][] = [
                    'title' => $reports['name'],
                    'url' => url_for('ext/mind_map_reports/view', 'id=' . $reports['id'] . '&path=' . $path)
                ];
            }
        }
    }
}
