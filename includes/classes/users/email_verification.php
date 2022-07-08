<?php

class email_verification
{
    static function check()
    {
        global $app_module_path, $app_user;

        if (CFG_PUBLIC_REGISTRATION_USER_ACTIVATION != 'email' or CFG_USE_PUBLIC_REGISTRATION != 1) {
            return true;
        }

        if (!app_session_is_registered('app_logged_users_id')) {
            return true;
        }

        if ($app_module_path == 'users/email_verification') {
            return true;
        }

        if ($app_user['is_email_verified'] == 0 and !in_array(
                $app_module_path,
                ['users/email_verification', 'users/login']
            )) {
            redirect_to('users/email_verification');
        }
    }

    static function check_if_user_email_is_updated()
    {
        global $app_user, $app_email_verification_code;

        if (isset($_POST['fields'][9]) and CFG_PUBLIC_REGISTRATION_USER_ACTIVATION == 'email' and CFG_USE_PUBLIC_REGISTRATION == 1) {
            if ($app_user['email'] != $_POST['fields'][9]) {
                $app_email_verification_code = '';

                db_query("update app_entity_1 set is_email_verified=0 where id='" . $app_user['id'] . "'");
            }
        }
    }

    static function send_code()
    {
        global $app_user, $app_users_cache, $app_email_verification_code;

        try {
            $code = $app_email_verification_code = str_pad(
                (string)random_int(0, (int)str_repeat('9', CFG_VERIFICATION_CODE_LENGTH)),
                CFG_VERIFICATION_CODE_LENGTH,
                '0',
                STR_PAD_LEFT
            );
        } catch (Exception $e) {
        }

        $users_info_query = db_query(
            "select * from app_entity_1 where id='" . db_input($app_user['id']) . "' and field_5=1"
        );
        if ($users_info = db_fetch_array($users_info_query) and isset($app_user['email'])) {
            $options = [
                'to' => $users_info['field_9'],
                'to_name' => $app_users_cache[$users_info['id']]['name'],
                'subject' => TEXT_EMAIL_VERIFICATION_EMAIL_SUBJECT,
                'body' => sprintf(TEXT_EMAIL_VERIFICATION_EMAIL_BODY, $code),
                'from' => CFG_EMAIL_ADDRESS_FROM,
                'from_name' => CFG_EMAIL_NAME_FROM,
                'send_directly' => true,
            ];

            users::send_email($options);
        }
    }

    static function approve()
    {
        global $app_user, $alerts;

        //set is_email_confirmed
        db_query("update app_entity_1 set is_email_verified=1 where id='" . $app_user['id'] . "'");

        $alerts->add(TEXT_EMAIL_VERIFIED, 'success');

        redirect_to('dashboard/');
    }
}