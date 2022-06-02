<?php

/**
 *add pivot reports to menu
 */
$reports_query = db_query("select * from app_ext_pivotreports order by sort_order, name");
while ($reports = db_fetch_array($reports_query)) {
    if (in_array($app_user['group_id'], explode(',', $reports['allowed_groups'])) or $app_user['group_id'] == 0) {
        $check_query = db_query(
            "select id from app_entities_menu where find_in_set('pivotreports" . $reports['id'] . "',reports_list)"
        );
        if (!$check = db_fetch_array($check_query)) {
            $app_plugin_menu['reports'][] = [
                'title' => $reports['name'],
                'url' => url_for('ext/pivotreports/view', 'id=' . $reports['id'])
            ];
        }
    }
}