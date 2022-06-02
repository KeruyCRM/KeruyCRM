<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_mail_accounts', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_mail_accounts');
    $obj['mailbox'] = 'INBOX';
}