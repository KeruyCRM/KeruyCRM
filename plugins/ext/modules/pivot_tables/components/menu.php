<?php

$reports_query = db_query("select * from app_ext_pivot_tables order by sort_order, name");
while ($reports = db_fetch_array($reports_query)) {
    $check_query = db_query(
        "select id from app_entities_menu where find_in_set('pivot_tables" . $reports['id'] . "',reports_list)"
    );
    if ($check = db_fetch_array($check_query)) {
        continue;
    }

    $pivot_table = new pivot_tables($reports);

    if ($pivot_table->has_access()) {
        if ($reports['in_menu']) {
            $app_plugin_menu['menu'][] = [
                'title' => $reports['name'],
                'url' => url_for('ext/pivot_tables/view', 'id=' . $reports['id']),
                'class' => 'fa-sitemap'
            ];
        } else {
            $app_plugin_menu['reports'][] = [
                'title' => $reports['name'],
                'url' => url_for('ext/pivot_tables/view', 'id=' . $reports['id'])
            ];
        }
    }
}