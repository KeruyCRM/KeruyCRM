<?php

if (!app_session_is_registered('processes_filter')) {
    $processes_filter = 0;
    app_session_register('processes_filter');
}

$app_title = app_set_title(TEXT_EXT_BUTTONS_GROUPS);

switch ($app_module_action) {
    case 'set_processes_filter':
        $processes_filter = $_POST['processes_filter'];

        redirect_to('ext/processes/buttons_groups');
        break;
    case 'save':
        $sql_data = [
            'name' => $_POST['name'],
            'entities_id' => $_POST['entities_id'],
            'button_color' => $_POST['button_color'],
            'button_icon' => $_POST['button_icon'],
            'button_position' => (isset($_POST['button_position']) ? implode(',', $_POST['button_position']) : ''),
            'sort_order' => $_POST['sort_order'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_processes_buttons_groups', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_processes_buttons_groups', $sql_data);

            $insert_id = db_insert_id();
        }

        redirect_to('ext/processes/buttons_groups');
        break;

    case 'delete':
        if (isset($_GET['id'])) {
            $obj = db_find('app_ext_processes_buttons_groups', $_GET['id']);

            db_query("delete from app_ext_processes_buttons_groups where id='" . db_input($_GET['id']) . "'");

            $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

            redirect_to('ext/processes/buttons_groups');
        }
        break;
}
