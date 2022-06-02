<?php

$app_title = app_set_title(TEXT_EXT_CHANGE_HISTORY);

switch ($app_module_action) {
    case 'save':
        $sql_data = [
            'is_active' => (isset($_POST['is_active']) ? 1 : 0),
            'name' => $_POST['name'],
            'menu_icon' => $_POST['menu_icon'],
            'position' => (isset($_POST['position']) ? implode(',', $_POST['position']) : ''),
            'users_groups' => (isset($_POST['users_groups']) ? implode(',', $_POST['users_groups']) : ''),
            'assigned_to' => (isset($_POST['assigned_to']) ? implode(',', $_POST['assigned_to']) : ''),
            'color_insert' => $_POST['color_insert'],
            'color_comment' => $_POST['color_comment'],
            'color_update' => $_POST['color_update'],
            'color_delete' => $_POST['color_delete'],
            'keep_history' => (int)$_POST['keep_history'],
            'rows_per_page' => (int)$_POST['rows_per_page'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_track_changes', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_track_changes', $sql_data);

            $insert_id = db_insert_id();
        }

        redirect_to('ext/track_changes/reports');
        break;

    case 'delete':
        if (isset($_GET['id'])) {
            $obj = db_find('app_ext_track_changes', $_GET['id']);

            db_query("delete from app_ext_track_changes where id='" . db_input($_GET['id']) . "'");
            db_query("delete from app_ext_track_changes_entities where reports_id='" . db_input($_GET['id']) . "'");
            db_query("delete from app_ext_track_changes_log where reports_id='" . db_input($_GET['id']) . "'");

            redirect_to('ext/track_changes/reports');
        }
        break;
}