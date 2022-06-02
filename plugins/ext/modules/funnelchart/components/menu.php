<?php
/**
 *add funnel chart reports to main menu
 */

if ($app_user['group_id'] > 0) {
    $reports_query = db_query(
        "select c.* from app_ext_funnelchart c, app_entities e where e.id=c.entities_id and (e.parent_id=0 or c.in_menu=1) and find_in_set(" . $app_user['group_id'] . ",c.users_groups) order by c.name"
    );
} else {
    $reports_query = db_query(
        "select c.* from app_ext_funnelchart c, app_entities e where e.id=c.entities_id and (e.parent_id=0 or c.in_menu=1) order by c.name"
    );
}

while ($reports = db_fetch_array($reports_query)) {
    $check_query = db_query(
        "select id from app_entities_menu where find_in_set('funnelchart" . $reports['id'] . "',reports_list)"
    );
    if (!$check = db_fetch_array($check_query)) {
        $app_plugin_menu['reports'][] = [
            'title' => $reports['name'],
            'url' => url_for('ext/funnelchart/view', 'id=' . $reports['id'])
        ];
    }
}

/**
 *add funnel chart reports to items menu
 */
if (isset($_GET['path'])) {
    $entities_list = items::get_sub_entities_list_by_path($_GET['path']);

    if (count($entities_list)) {
        if ($app_user['group_id'] > 0) {
            $reports_query = db_query(
                "select c.* from app_ext_funnelchart c, app_entities e where e.id=c.entities_id and e.id in (" . implode(
                    ',',
                    $entities_list
                ) . ")  and find_in_set(" . $app_user['group_id'] . ",c.users_groups) order by c.name"
            );
        } else {
            $reports_query = db_query(
                "select c.* from app_ext_funnelchart c, app_entities e where e.id=c.entities_id and  e.id in (" . implode(
                    ',',
                    $entities_list
                ) . ")  order by c.name"
            );
        }

        while ($reports = db_fetch_array($reports_query)) {
            $path = app_get_path_to_report($reports['entities_id']);

            $app_plugin_menu['items_menu_reports'][] = [
                'title' => $reports['name'],
                'url' => url_for('ext/funnelchart/view', 'id=' . $reports['id'] . '&path=' . $path)
            ];
        }
    }
}