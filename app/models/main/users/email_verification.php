<?php

namespace Models\Main\Users;

class Email_verification
{
    public static function check()
    {
        if (\K::$fw->CFG_PUBLIC_REGISTRATION_USER_ACTIVATION != 'email' or \K::$fw->CFG_USE_PUBLIC_REGISTRATION != 1) {
            return true;
        }

        //if (!app_session_is_registered('app_logged_users_id')) {
        if (!\K::app_session_is_registered('app_logged_users_id')) {
            return true;
        }

        if (\K::$fw->app_module_path == 'users/email_verification') {
            return true;
        }

        if (\K::$fw->app_user['is_email_verified'] == 0 and !in_array(
                \K::$fw->app_module_path,
                ['users/email_verification', 'users/login']
            )) {
            \Helpers\Urls::redirect_to('main/users/email_verification');
        }
    }

    public static function check_if_user_email_is_updated()
    {
        //global $app_user, $app_email_verification_code;

        if (\K::fw()->exists('POST.fields.9')
            and \K::$fw->CFG_PUBLIC_REGISTRATION_USER_ACTIVATION == 'email'
            and \K::$fw->CFG_USE_PUBLIC_REGISTRATION == 1) {
            if (\K::$fw->app_user['email'] != \K::$fw->{'POST.fields.9'}) {
                \K::$fw->app_email_verification_code = '';

                //db_query("update app_entity_1 set is_email_verified=0 where id='" . \K::$fw->app_user['id'] . "'");

                \K::model()->db_perform(
                    'app_entity_1',
                    ['is_email_verified' => 0],
                    ['id = ?', \K::$fw->app_user['id']]
                );
            }
        }
    }

    public static function send_code()
    {
        //global $app_user, $app_users_cache, $app_email_verification_code;

        try {
            $code = \K::$fw->app_email_verification_code = str_pad(
                (string)random_int(0, (int)str_repeat('9', \K::$fw->CFG_VERIFICATION_CODE_LENGTH)),
                \K::$fw->CFG_VERIFICATION_CODE_LENGTH,
                '0',
                STR_PAD_LEFT
            );
        } catch (\Exception $e) {
        }

        /*$users_info_query = db_query(
            "select * from app_entity_1 where id='" . db_input($app_user['id']) . "' and field_5=1"
        );*/

        $users_info = \K::model()->db_fetch_one('app_entity_1', [
            'id = ? and field_5 = 1',
            \K::$fw->app_user['id']
        ], [], 'id,field_9');

        if ($users_info and isset(\K::$fw->app_user['email'])) {
            $options = [
                'to' => \K::$fw->users_info['field_9'],
                'to_name' => \K::$fw->app_users_cache[$users_info['id']]['name'],
                'subject' => \K::$fw->TEXT_EMAIL_VERIFICATION_EMAIL_SUBJECT,
                'body' => sprintf(\K::$fw->TEXT_EMAIL_VERIFICATION_EMAIL_BODY, $code),
                'from' => \K::$fw->CFG_EMAIL_ADDRESS_FROM,
                'from_name' => \K::$fw->CFG_EMAIL_NAME_FROM,
                'send_directly' => true,
            ];

            \Models\Main\Users\Users::send_email($options);
        }
    }

    public static function approve()
    {
        //set is_email_confirmed
        //db_query("update app_entity_1 set is_email_verified=1 where id='" . \K::$fw->app_user['id'] . "'");

        \K::model()->db_perform(
            'app_entity_1',
            ['is_email_verified' => 1],
            ['id = ?', \K::$fw->app_user['id']]
        );

        \K::flash()->addMessage(\K::$fw->TEXT_EMAIL_VERIFIED, 'success');

        \Helpers\Urls::redirect_to('main/dashboard');
    }
}