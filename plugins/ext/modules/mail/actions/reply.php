<?php

$email_info_query = db_query(
    "select m.*, ma.name as account_name, if(length(ma.email)>0,ma.email, ma.login) as account_login from app_ext_mail m left join app_ext_mail_accounts ma on ma.id=m.accounts_id  where m.id='" . _get::int(
        'mail_id'
    ) . "' and m.accounts_id in (select mau.accounts_id from app_ext_mail_accounts_users mau where mau.users_id='" . $app_user['id'] . "')"
);
if (!$email_info = db_fetch_array($email_info_query)) {
    redirect_to('dashboard/access_forbidden');
}


switch ($app_module_action) {
    case 'send':

        $accounts_id = _post::int('accounts_id');

        $mail_account = db_find('app_ext_mail_accounts', $accounts_id);

        $mail_to = $_POST['mail_to'];

        $subject = db_prepare_html_input($_POST['subject']);
        $body = db_prepare_html_input($_POST['body']);

        $body = str_replace(
            '<blockquote>',
            '<blockquote style="margin: 0px 0px 0px 0.8ex; border-left: 1px solid rgb(204,204,204); padding-left: 1ex;">',
            $body
        );

        $attachments = [];

        if (isset($_POST['message_attachments'])) {
            if (strlen($_POST['message_attachments'])) {
                foreach (explode(',', $_POST['message_attachments']) as $file) {
                    $file_info = mail_info::parse_attachment_filename($file);
                    $attachments[$file_info['file_path']] = $file_info['name'];
                }
            }
        }

        //print_r($attachments);
        //exit();

        $send_check = true;
        $error_msg = [];
        $to_names = [];
        $to_emails = [];
        foreach ($mail_to as $mail_to_value) {
            if (preg_match('/([^<]+)<([^>]+)>/', $mail_to_value, $regs)) {
                $to_name = trim($regs[1]);
                $to_email = trim($regs[2]);

                $to_names[] = trim($regs[1]);
                $to_emails[] = trim($regs[2]);
            } else {
                $to_name = '';
                $to_email = trim($mail_to_value);

                $to_names[] = '';
                $to_emails[] = trim($mail_to_value);
            }

            $options = [
                'from' => (strlen($mail_account['email']) ? $mail_account['email'] : $mail_account['login']),
                'from_name' => $mail_account['name'],
                'to' => $to_email,
                'to_name' => $to_name,
                'subject' => $subject,
                'body' => $body,
                'attachments' => $attachments,
            ];

            //print_rr($options);
            //exit();
            //print_rr($options);
            //exit();

            $response = mail_accounts::send_mail($mail_account, $options);


            if ($response['status'] == 'error') {
                $text = $to_email . ': ' . $response['text'];
                $alerts->add($text, 'error');
                $error_msg[] = str_replace('https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting', '', $text);
                $send_check = false;
            } else {
                //auto add contacts
                foreach ($to_emails as $k => $email) {
                    $contacts_query = db_query(
                        "select id from app_ext_mail_contacts where email = '" . db_input($email) . "'"
                    );
                    if (!$contacts = db_fetch_array($contacts_query)) {
                        $data = [
                            'email' => $email,
                            'name' => $to_names[$k],
                            'accounts_id' => $accounts_id,
                        ];
                        db_perform('app_ext_mail_contacts', $data);
                    }
                }
            }
        }

        //exit();

        if ($send_check) {
            $alerts->add(TEXT_EXT_MESSAGE_SENT, 'success');
        }

        $mail_data = [
            'date_added' => time(),
            'is_new' => 0,
            'is_sent' => 1,
            'groups_id' => $email_info['groups_id'],
            'accounts_id' => $accounts_id,
            'subject' => $subject,
            'subject_cropped' => mail_info::crop_subject($subject),
            'body' => $body,
            'body_text' => '',
            'attachments' => (isset($_POST['message_attachments']) ? $_POST['message_attachments'] : ''),
            'to_name' => implode(',', $to_names),
            'to_email' => implode(',', $to_emails),
            'from_name' => $mail_account['name'],
            'from_email' => $mail_account['login'],
            'error_msg' => implode('<br>', $error_msg),
        ];

        db_perform('app_ext_mail', $mail_data);

        redirect_to('ext/mail/info', 'id=' . $email_info['groups_id']);

        break;
}