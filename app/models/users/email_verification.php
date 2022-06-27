<?php

namespace Models\Users;

class Email_verification
{
    public static function check()
    {
        if (\K::$fw->CFG_PUBLIC_REGISTRATION_USER_ACTIVATION != 'email' or \K::$fw->CFG_USE_PUBLIC_REGISTRATION != 1) {
            return true;
        }

        //if (!app_session_is_registered('app_logged_users_id')) {
        if (!\K::sessionExists('app_logged_users_id')) {
            return true;
        }

        if (\K::$fw->app_module_path == 'users/email_verification') {
            return true;
        }

        if (\K::$fw->app_user['is_email_verified'] == 0 and !in_array(
                \K::$fw->app_module_path,
                ['users/email_verification', 'users/login']
            )) {
            //redirect_to('users/email_verification');
            \K::fw()->reroute('/users/email_verification');
        }
    }

    public static function check_if_user_email_is_updated()
    {
        global $app_user, $app_email_verification_code;

        if (isset($_POST['fields'][9]) and \K::$fw->CFG_PUBLIC_REGISTRATION_USER_ACTIVATION == 'email' and \K::f3(
            )->CFG_USE_PUBLIC_REGISTRATION == 1) {
            if ($app_user['email'] != $_POST['fields'][9]) {
                $app_email_verification_code = '';

                db_query("update app_entity_1 set is_email_verified=0 where id='" . $app_user['id'] . "'");
            }
        }
    }

    public static function send_code()
    {
        global $app_user, $app_users_cache, $app_email_verification_code;

        $code = $app_email_verification_code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);

        $users_info_query = db_query(
            "select * from app_entity_1 where id='" . db_input($app_user['id']) . "' and field_5=1"
        );
        if ($users_info = db_fetch_array($users_info_query) and isset($app_user['email'])) {
            $options = [
                'to' => $users_info['field_9'],
                'to_name' => $app_users_cache[$users_info['id']]['name'],
                'subject' => \K::$fw->TEXT_EMAIL_VERIFICATION_EMAIL_SUBJECT,
                'body' => sprintf(\K::$fw->TEXT_EMAIL_VERIFICATION_EMAIL_BODY, $code),
                'from' => \K::$fw->CFG_EMAIL_ADDRESS_FROM,
                'from_name' => \K::$fw->CFG_EMAIL_NAME_FROM,
                'send_directly' => true,
            ];

            users::send_email($options);
        }
    }

    public static function approve()
    {
        global $app_user, $alerts;

        //set is_email_confirmed
        db_query("update app_entity_1 set is_email_verified=1 where id='" . $app_user['id'] . "'");

        $alerts->add(\K::$fw->TEXT_EMAIL_VERIFIED, 'success');

        redirect_to('dashboard/');
    }
}