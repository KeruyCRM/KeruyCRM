<?php

//check if user is logged
if (!app_session_is_registered(
        'app_logged_users_id'
    ) or CFG_PUBLIC_REGISTRATION_USER_ACTIVATION != 'email' or CFG_USE_PUBLIC_REGISTRATION != 1) {
    redirect_to('users/login');
}

//check if is checked
if ($app_user['is_email_verified'] == 1) {
    redirect_to('dashboard/');
}

if (!strlen($app_email_verification_code)) {
    email_verification::send_code();
}

switch ($app_module_action) {
    case 'resend';
        $alerts->add(TEXT_RESEND_CODE_TIP);
        redirect_to('users/login');
        break;
    case 'update_email':
        if (app_validate_email($_POST['email'])) {
            $check_query = db_query(
                "select count(*) as total from app_entity_1 where field_9='" . db_input(
                    $_POST['email']
                ) . "'  and id!='" . db_input($app_user['id']) . "'"
            );
            $check = db_fetch_array($check_query);
            if ($check['total'] > 0) {
                $alerts->add(TEXT_ERROR_USEREMAL_EXIST, 'warning');
            } elseif ($app_user['email'] != $_POST['email']) {
                //update account
                db_query(
                    "update app_entity_1 set field_9='" . db_input(
                        $_POST['email']
                    ) . "' where id='" . $app_user['id'] . "'"
                );

                $alerts->add(TEXT_ACCOUNT_UPDATED, 'success');

                //reset verification code
                $app_email_verification_code = '';
            }
        }

        redirect_to('users/email_verification');
        break;
    case 'check':

        //chck form token
        app_check_form_token('users/login');

        if ($app_email_verification_code == $_POST['code']) {
            email_verification::approve();
        } else {
            $alerts->add(TEXT_INCORRECT_CODE, 'error');
            redirect_to('users/email_verification');
        }

        break;
}

$app_layout = 'public_layout.php';