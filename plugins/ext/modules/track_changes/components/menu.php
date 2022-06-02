<?php

$reports_query = db_query(
    "select * from app_ext_track_changes where is_active=1 and (find_in_set('" . $app_user['group_id'] . "',users_groups) or find_in_set('" . $app_user['id'] . "',assigned_to))"
);
while ($reports = db_fetch_array($reports_query)) {
    foreach (explode(',', $reports['position']) as $position) {
        switch ($position) {
            case 'in_menu':
                $app_plugin_menu['menu'][] = [
                    'title' => $reports['name'],
                    'url' => url_for('ext/track_changes/view', 'reports_id=' . $reports['id']),
                    'class' => $reports['menu_icon']
                ];
                break;
            case 'in_reports_menu':
                $app_plugin_menu['reports'][] = [
                    'title' => $reports['name'],
                    'url' => url_for('ext/track_changes/view', 'reports_id=' . $reports['id'])
                ];
                break;
        }
    }
}


