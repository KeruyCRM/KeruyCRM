<?php

require(component_path('ext/recurring_tasks/check_access'));

switch ($app_module_action) {
    case 'save':

        $repeat_interval = (int)$_POST['repeat_interval'];

        $sql_data = [
            'entities_id' => $current_entity_id,
            'items_id' => $current_item_id,
            'is_active' => (isset($_POST['is_active']) ? 1 : 0),
            'repeat_type' => $_POST['repeat_type'],
            'repeat_time' => $_POST['repeat_time'],
            'repeat_interval' => ($repeat_interval > 0 ? $repeat_interval : 1),
            'repeat_days' => ((isset($_POST['repeat_days']) and $_POST['repeat_type'] == 'weekly') ? implode(
                ',',
                $_POST['repeat_days']
            ) : ''),
            'repeat_start' => (isset($_POST['repeat_start']) ? get_date_timestamp($_POST['repeat_start']) : ''),
            'repeat_end' => (isset($_POST['repeat_end']) ? get_date_timestamp($_POST['repeat_end']) : ''),
            'repeat_limit' => $_POST['repeat_limit'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_recurring_tasks', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            $sql_data['date_added'] = time();
            $sql_data['created_by'] = $app_user['id'];
            db_perform('app_ext_recurring_tasks', $sql_data);
        }

        redirect_to('ext/recurring_tasks/repeat', 'path=' . $app_path);

        break;
    case 'delete':

        $tasks_query = db_query(
            "select id from app_ext_recurring_tasks where id='" . db_input(
                $_GET['id']
            ) . "' and entities_id='" . $current_entity_id . "' and items_id='" . $current_item_id . "'"
        );
        if ($tasks = db_fetch_array($tasks_query)) {
            db_query("delete from app_ext_recurring_tasks where id='" . $tasks['id'] . "'");
            db_query("delete from app_ext_recurring_tasks_fields where tasks_id='" . $tasks['id'] . "'");
        }

        redirect_to('ext/recurring_tasks/repeat', 'path=' . $app_path);
        break;
}