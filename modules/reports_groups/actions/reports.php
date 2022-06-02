<?php

if (!users::has_reports_access()) {
    redirect_to('dashboard/access_forbidden');
}

$app_title = app_set_title(TEXT_REPORTS_GROUPS);

switch ($app_module_action) {
    case 'save':
        $sql_data = [
            'name' => db_prepare_input($_POST['name']),
            'menu_icon' => db_prepare_input($_POST['menu_icon']),
            'icon_color' => db_prepare_input($_POST['icon_color']),
            'bg_color' => db_prepare_input($_POST['bg_color']),
            'in_menu' => (isset($_POST['in_menu']) ? $_POST['in_menu'] : 0),
            'in_dashboard' => (isset($_POST['in_dashboard']) ? $_POST['in_dashboard'] : 0),
            'sort_order' => $_POST['sort_order'],
            'created_by' => $app_user['id'],
            'is_common' => 0,
        ];

        if (isset($_GET['id'])) {
            db_perform('app_reports_groups', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_reports_groups', $sql_data);
        }

        redirect_to('reports_groups/reports');
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            db_delete_row('app_reports_groups', $_GET['id']);

            redirect_to('reports_groups/reports');
        }
        break;
}		
