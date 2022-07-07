<?php

namespace Models\Main\Users;

class Two_step_verification
{
    public static function check()
    {
        if (\K::$fw->CFG_2STEP_VERIFICATION_ENABLED != 1) {
            return true;
        }

        if (!\K::app_session_is_registered('app_logged_users_id')) {
            return true;
        }

        if (\K::$fw->app_module_path == 'users/2step_verification') {
            return true;
        }

        //skip for guest user
        if (\K::$fw->CFG_ENABLE_GUEST_LOGIN == 1 and \K::$fw->CFG_GUEST_LOGIN_USER == \K::$fw->app_user['id']) {
            return true;
        }

        if (!isset(\K::$fw->two_step_verification_info['is_checked']) and !in_array(
                \K::$fw->app_module_path,
                ['users/2step_verification', 'users/login']
            )) {
            //redirect_to('users/2step_verification');
            \K::reroute('users/2step_verification');
        }
    }

    public static function send_code()
    {
        global $app_user, $app_users_cache, $two_step_verification_info;

        $code = '';
        try {
            $code = $two_step_verification_info['code'] = str_pad(
                (string)random_int(0, (int)str_repeat('9', \K::$fw->CFG_VERIFICATION_CODE_LENGTH)),
                \K::$fw->CFG_VERIFICATION_CODE_LENGTH,
                '0',
                STR_PAD_LEFT
            );
        } catch (\Exception $e) {
        }

        switch (\K::$fw->CFG_2STEP_VERIFICATION_TYPE) {
            case 'email':
                $users_info_query = db_query(
                    "select * from app_entity_1 where id='" . db_input($app_user['id']) . "' and field_5=1"
                );
                if ($users_info = db_fetch_array($users_info_query) and isset($app_user['email'])) {
                    $options = [
                        'to' => $users_info['field_9'],
                        'to_name' => $app_users_cache[$users_info['id']]['name'],
                        'subject' => \K::$fw->TEXT_2STEP_VERIFICATION_EMAIL_SUBJECT,
                        'body' => sprintf(\K::$fw->TEXT_2STEP_VERIFICATION_EMAIL_BODY, $code),
                        'from' => \K::$fw->CFG_EMAIL_ADDRESS_FROM,
                        'from_name' => \K::$fw->TEXT_EMAIL_NAME_FROM,
                        'send_directly' => true,
                    ];

                    users::send_email($options);
                }
                break;

            case 'sms':
                $users_info_query = db_query(
                    "select * from app_entity_1 where id='" . db_input($app_user['id']) . "' and field_5=1"
                );
                if ($users_info = db_fetch_array($users_info_query) and isset($app_user['email'])) {
                    $module_id = \K::$fw->CFG_2STEP_VERIFICATION_SMS_MODULE;
                    $phone = (isset(
                        $users_info['field_' . \K::$fw->CFG_2STEP_VERIFICATION_USER_PHONE]
                    ) ? $users_info['field_' . \K::$fw->CFG_2STEP_VERIFICATION_USER_PHONE] : '');

                    if (strlen($phone)) {
                        $message_text = \K::$fw->TEXT_2STEP_VERIFICATION_EMAIL_SUBJECT . '. ' . sprintf(
                                \K::$fw->TEXT_2STEP_VERIFICATION_EMAIL_BODY,
                                $code
                            );
                        sms::send_by_module($module_id, $phone, $message_text);
                    } else {
                        self::approve();
                    }
                }
                break;
        }
    }

    public static function approve()
    {
        global $two_step_verification_info, $app_user;

        $two_step_verification_info['is_checked'] = true;

        users_login_log::success($app_user['username'], $app_user['id']);

        if (isset($_COOKIE['app_login_redirect_to'])) {
            redirect_to(str_replace('module=', '', $_COOKIE['app_login_redirect_to']));
        } else {
            redirect_to('dashboard/');
        }
    }
}