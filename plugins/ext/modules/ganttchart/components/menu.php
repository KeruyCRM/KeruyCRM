<?php

/**
 *add gantt reports to menu
 */
if ($app_user['group_id'] > 0) {
    $reports_query = db_query(
        "select g.* from app_ext_ganttchart g, app_entities e, app_ext_ganttchart_access ga where e.id=g.entities_id and g.id=ga.ganttchart_id and ga.access_groups_id='" . db_input(
            $app_user['group_id']
        ) . "' order by name"
    );
} else {
    $reports_query = db_query(
        "select g.* from app_ext_ganttchart g, app_entities e where e.id=g.entities_id order by g.name"
    );
}

while ($reports = db_fetch_array($reports_query)) {
    $check_query = db_query(
        "select id from app_entities_menu where find_in_set('ganttreport" . $reports['id'] . "',reports_list)"
    );
    if (!$check = db_fetch_array($check_query)) {
        $app_plugin_menu['reports'][] = [
            'title' => $reports['name'],
            'url' => url_for('ext/ganttchart/dhtmlx', 'id=' . $reports['id'])
        ];
    }
}


/**
 *add gantt reports to items menu
 */
if (isset($_GET['path'])) {
    $entities_list = items::get_sub_entities_list_by_path($_GET['path']);

    if (count($entities_list) > 0) {
        if ($app_user['group_id'] > 0) {
            $reports_query = db_query(
                "select g.* from app_ext_ganttchart g, app_entities e, app_ext_ganttchart_access ga where e.id=g.entities_id and e.id in (" . implode(
                    ',',
                    $entities_list
                ) . ") and g.id=ga.ganttchart_id and ga.access_groups_id='" . db_input(
                    $app_user['group_id']
                ) . "' order by name"
            );
        } else {
            $reports_query = db_query(
                "select g.* from app_ext_ganttchart g, app_entities e where e.id=g.entities_id and e.id in (" . implode(
                    ',',
                    $entities_list
                ) . ") order by g.name"
            );
        }

        while ($reports = db_fetch_array($reports_query)) {
            $path = app_get_path_to_report($reports['entities_id']);

            $app_plugin_menu['items_menu_reports'][] = [
                'title' => $reports['name'],
                'url' => url_for('ext/ganttchart/dhtmlx', 'id=' . $reports['id'] . '&path=' . $path)
            ];
        }
    }
}