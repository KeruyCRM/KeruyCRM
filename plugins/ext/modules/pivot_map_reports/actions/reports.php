<?php

//check access
if ($app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}

switch ($app_module_action) {
    case 'save':

        $sql_data = [
            'name' => $_POST['name'],
            'users_groups' => (isset($_POST['access']) ? json_encode($_POST['access']) : ''),
            'in_menu' => (isset($_POST['in_menu']) ? $_POST['in_menu'] : 0),
            'users_groups' => (isset($_POST['users_groups']) ? implode(',', $_POST['users_groups']) : ''),
            'zoom' => $_POST['zoom'],
            'latlng' => trim(preg_replace('/ +/', ',', $_POST['latlng'])),
            'display_legend' => $_POST['display_legend'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_pivot_map_reports', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_pivot_map_reports', $sql_data);
        }

        redirect_to('ext/pivot_map_reports/reports');

        break;
    case 'delete':
        $obj = db_find('app_ext_pivot_map_reports', $_GET['id']);

        db_delete_row('app_ext_pivot_map_reports', $_GET['id']);

        $entities_query = db_query(
            "select id from app_ext_pivot_map_reports_entities where reports_id='" . $_GET['id'] . "'"
        );
        while ($entities = db_fetch_array($entities_query)) {
            reports::delete_reports_by_type('pivot_map' . $entities['id']);
        }

        db_delete_row('app_ext_pivot_map_reports_entities', $_GET['id'], 'reports_id');

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('ext/pivot_map_reports/reports');
        break;
}