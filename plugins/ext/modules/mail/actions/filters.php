<?php

if (!mail_accounts::user_has_access()) {
    redirect_to('dashboard/access_forbidden');
}

switch ($app_module_action) {
    case 'save':
        $sql_data = [
            'accounts_id' => $_POST['accounts_id'],
            'from_email' => $_POST['from_email'],
            'has_words' => $_POST['has_words'],
            'action' => $_POST['action'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_mail_filters', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_mail_filters', $sql_data);
        }

        redirect_to('ext/mail/filters');
        break;
    case 'delete':

        db_delete_row('app_ext_mail_filters', _get::int('id'));

        redirect_to('ext/mail/filters');
        break;
}