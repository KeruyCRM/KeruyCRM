<?php

switch ($app_module_action) {
    case 'send':

        $options = [
            'to' => $_POST['send_to'],
            'to_name' => '',
            'subject' => TEXT_TEST_EMAIL_SUBJECT,
            'body' => TEXT_TEST_EMAIL_SUBJECT,
            'from' => $app_user['email'],
            'from_name' => $app_user['name'],
            'send_directly' => true,
        ];

        if (users::send_email($options)) {
            $alerts->add(TEXT_EMAIL_SENT, 'success');
        }

        redirect_to('configuration/emails_send_test', 'send_to=' . $_POST['send_to']);
        break;
}
