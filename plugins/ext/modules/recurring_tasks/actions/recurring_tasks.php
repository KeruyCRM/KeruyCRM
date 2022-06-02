<?php

//check access
if ($app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}

switch ($app_module_action) {
    case 'save':

        $repeat_interval = (int)$_POST['repeat_interval'];

        $sql_data = [
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
        }

        redirect_to('ext/recurring_tasks/recurring_tasks');

        break;
    case 'delete':

        db_query("delete from app_ext_recurring_tasks where id='" . db_input($_GET['id']) . "'");
        db_query("delete from app_ext_recurring_tasks_fields where tasks_id='" . $_GET['id'] . "'");

        redirect_to('ext/recurring_tasks/recurring_tasks');
        break;
}