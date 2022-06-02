<?php

$accounts_info_query = db_query("select * from app_ext_mail_accounts where id='" . _get::int('accounts_id') . "'");
if (!$accounts_info = db_fetch_array($accounts_info_query)) {
    redirect_to('ext/mail_integration/accounts');
}

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_mail_accounts_users', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_mail_accounts_users');
}