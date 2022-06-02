<?php

if (!extension_loaded('imap')) {
    $alerts->add(TEXT_EXT_IMAP_EXTENSION_IS_REQUIRED, 'error');
    redirect_to('ext/mail_integration/settings');
}

switch ($app_module_action) {
    case 'save':

        $sql_data = [
            'is_active' => (isset($_POST['is_active']) ? 1 : 0),
            'name' => $_POST['name'],
            'bg_color' => $_POST['bg_color'],
            'is_default' => (isset($_POST['is_default']) ? 1 : 0),
            'imap_server' => $_POST['imap_server'],
            'mailbox' => $_POST['mailbox'],
            'login' => $_POST['login'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'delete_emails' => (isset($_POST['delete_emails']) ? 1 : 0),
            'not_group_by_subject' => (isset($_POST['not_group_by_subject']) ? 1 : 0),
            'use_smtp' => $_POST['use_smtp'],
            'smtp_server' => $_POST['smtp_server'],
            'smtp_port' => $_POST['smtp_port'],
            'smtp_encryption' => $_POST['smtp_encryption'],
            'smtp_login' => $_POST['smtp_login'],
            'smtp_password' => $_POST['smtp_password'],
            'send_autoreply' => $_POST['send_autoreply'],
            'autoreply_msg' => $_POST['autoreply_msg'],
        ];

        //reset defaults
        if (isset($_POST['is_default'])) {
            db_query("update app_ext_mail_accounts set is_default=0");
        }

        if (isset($_GET['id'])) {
            db_perform('app_ext_mail_accounts', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_mail_accounts', $sql_data);
        }

        redirect_to('ext/mail_integration/accounts');

        break;
    case 'delete':
        $obj = db_find('app_ext_mail_accounts', $_GET['id']);

        db_delete_row('app_ext_mail_accounts', $_GET['id']);
        db_delete_row('app_ext_mail_accounts_users', $_GET['id'], 'accounts_id');

        $entities_query = db_query(
            "select id from app_ext_mail_accounts_entities where accounts_id='" . _get::int('id') . "'"
        );
        while ($entities = db_fetch_array($entities_query)) {
            db_delete_row('app_ext_mail_accounts_entities_fields', $entities['id'], 'account_entities_id');
        }

        db_delete_row('app_ext_mail_accounts_entities', _get::int('id'), 'accounts_id');
        db_delete_row('app_ext_mail_contacts', _get::int('id'), 'accounts_id');

        db_query(
            "delete from  app_ext_mail_groups_from where mail_groups_id in (select id from app_ext_mail_groups where accounts_id = '" . _get::int(
                'id'
            ) . "')"
        );
        db_delete_row('app_ext_mail_groups', _get::int('id'), 'accounts_id');

        db_delete_row('app_ext_mail_filters', _get::int('id'), 'accounts_id');

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('ext/mail_integration/accounts');
        break;
    case 'clear':
        $accounts_id = _GET('id');

        //delete attachments
        $mail_query = db_query(
            "select attachments from app_ext_mail where  length(attachments)>0  and accounts_id='" . $accounts_id . "'"
        );
        while ($mail = db_fetch_array($mail_query)) {
            foreach (explode(',', $mail['attachments']) as $filename) {
                $file = mail_info::parse_attachment_filename($filename);

                if (is_file($file['file_path'])) {
                    unlink($file['file_path']);
                }
            }
        }

        //delete all emails by account
        db_query("delete from app_ext_mail where accounts_id='" . $accounts_id . "'");

        //reset mail groups
        db_query(
            "delete from app_ext_mail_groups_from where mail_groups_id in (select mg.id from app_ext_mail_groups mg where (select count(*) from app_ext_mail m where m.groups_id=mg.id)=0)"
        );
        db_query(
            "delete from app_ext_mail_to_items where mail_groups_id in (select mg.id from app_ext_mail_groups mg where (select count(*) from app_ext_mail m where m.groups_id=mg.id)=0)"
        );
        db_query(
            "delete from app_ext_mail_groups where id in (select mg.id from app_ext_mail_groups mg where (select count(*) from app_ext_mail m where m.groups_id=mg.id)=0)"
        );

        $alerts->add(TEXT_EXT_RECORDS_DELETED, 'success');

        redirect_to('ext/mail_integration/accounts');
        break;
}