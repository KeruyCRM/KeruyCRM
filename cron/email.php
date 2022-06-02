<?php

chdir(substr(__DIR__, 0, -5));

define('IS_CRON', true);

//load core
require('includes/application_core.php');

//load app lagn
if (is_file($v = 'includes/languages/' . CFG_APP_LANGUAGE)) {
    require($v);
}

$app_users_cache = users::get_cache();

$emails_limit = (int)CFG_MAXIMUM_NUMBER_EMAILS;

$emails_query = db_query(
    "select * from app_emails_on_schedule order by id limit " . ($emails_limit > 0 ? $emails_limit : 1)
);
while ($emails = db_fetch_array($emails_query)) {
    $attachments = [];

    //include attachments
    if (strlen($emails['email_attachments'])) {
        foreach (explode(',', $emails['email_attachments']) as $filename) {
            $attachments[DIR_FS_TMP . sha1($filename)] = $filename;
        }
    }

    $options = [
        'to' => $emails['email_to'],
        'to_name' => $emails['email_to_name'],
        'subject' => $emails['email_subject'],
        'body' => $emails['email_body'],
        'from' => $emails['email_from'],
        'from_name' => $emails['email_from_name'],
        'attachments' => $attachments,
        'send_directly' => true,
    ];

    users::send_email($options);

    db_delete_row('app_emails_on_schedule', $emails['id']);

    //reset attachments
    foreach ($attachments as $filepath => $filename) {
        if (is_file($filepath)) {
            //check if there are other emails with this $filename
            $check_query = db_query(
                "select id from app_emails_on_schedule where find_in_set('" . db_input(
                    $filename
                ) . "',email_attachments) limit 1"
            );
            if (!$check = db_fetch_array($check_query)) {
                unlink($filepath);
            }
        }
    }
}