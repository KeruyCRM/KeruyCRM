<?php

switch ($app_module_action) {
    case 'save':

        $sql_data = [
            'name' => $_POST['name'],
            'description' => $_POST['description'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_email_rules_blocks', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_email_rules_blocks', $sql_data);
        }

        redirect_to('ext/email_sending/blocks', 'entities_id=' . _get::int('entities_id'));

        break;
    case 'delete':

        if (isset($_GET['id'])) {
            db_delete_row('app_ext_email_rules_blocks', $_GET['id']);
        }

        redirect_to('ext/email_sending/blocks', 'entities_id=' . _get::int('entities_id'));
        break;
}