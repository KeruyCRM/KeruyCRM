<?php

$accounts_info_query = db_query("select * from app_ext_mail_accounts where id='" . _get::int('accounts_id') . "'");
if (!$accounts_info = db_fetch_array($accounts_info_query)) {
    redirect_to('ext/mail_integration/accounts');
}

switch ($app_module_action) {
    case 'save':

        $sql_data = [
            'accounts_id' => $accounts_info['id'],
            'users_id' => $_POST['users_id'],
            'send_mail_as' => $_POST['send_mail_as'],
            'signature' => $_POST['signature'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_mail_accounts_users', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_mail_accounts_users', $sql_data);
        }

        //rest of list of users who has access to mail
        mail_accounts_users::reset_cfg();

        redirect_to('ext/mail_integration/accounts_users', 'accounts_id=' . $accounts_info['id']);

        break;
    case 'delete':

        db_delete_row('app_ext_mail_accounts_users', $_GET['id']);

        //rest of list of users who has access to mail
        mail_accounts_users::reset_cfg();

        redirect_to('ext/mail_integration/accounts_users', 'accounts_id=' . $accounts_info['id']);
        break;
}