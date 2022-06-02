<?php

switch ($app_module_action) {
    case 'check_name':
        $name = $_GET['name'];
        $id = $_GET['id'];
        $check_query = db_query(
            "select id from app_global_vars where name='" . db_input($name) . "'" . ($id ? " and id!={$id}" : '')
        );
        if ($check = db_fetch_array($check_query)) {
            echo json_encode(TEXT_UNIQUE_FIELD_VALUE_ERROR);
        } else {
            echo json_encode(true);
        }

        exit();

        break;
    case 'save':

        $is_folder = $_POST['is_folder'] ?? 0;
        $name = $_POST['name'] ?? '';
        $folder_name = $_POST['folder_name'] ?? '';

        if (!$is_folder) {
            $name = preg_replace('/_+/', '_', preg_replace('/[^\w_]+/u', '', preg_replace('/\s+/', '_', trim($name))));
        }

        $sql_data = [
            'parent_id' => (isset($_POST['parent_id']) ? $_POST['parent_id'] : 0),
            'is_folder' => $is_folder,
            'name' => ($is_folder ? $folder_name : $name),
            'value' => $_POST['value'] ?? '',
            'notes' => $_POST['notes'] ?? '',
            'sort_order' => $_POST['sort_order'],

        ];

        if (isset($_GET['id'])) {
            db_perform('app_global_vars', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_global_vars', $sql_data);
        }

        redirect_to('global_vars/vars');

        break;
    case 'delete':
        $obj = db_find('app_global_vars', $_GET['id']);

        db_delete_row('app_global_vars', $_GET['id']);

        db_query("update app_global_vars set parent_id=0 where parent_id='" . _get::int('id') . "'");

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('global_vars/vars');
        break;
}
    