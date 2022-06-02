<?php

if (!mail_accounts::user_has_access()) {
    redirect_to('dashboard/access_forbidden');
}

if (isset($_GET['mail_id'])) {
    $email_info_query = db_query(
        "select * from app_ext_mail where id='" . _get::int(
            'mail_id'
        ) . "' and accounts_id in (select accounts_id from app_ext_mail_accounts_users where users_id='" . $app_user['id'] . "')"
    );
    if (!$email_info = db_fetch_array($email_info_query)) {
        redirect_to('dashboard/access_forbidden');
    }
}


$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_mail_filters', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_mail_filters');

    $obj['accounts_id'] = ($app_mail_filters['accounts_id'] > 0 ? $app_mail_filters['accounts_id'] : mail_accounts::get_default(
    ));
}