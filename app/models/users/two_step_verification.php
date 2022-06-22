<?php

namespace Models\Users;

class Two_step_verification
{
    public static function check()
    {
        global $app_module_path, $two_step_verification_info, $app_user;

        if (\K::f3()->CFG_2STEP_VERIFICATION_ENABLED != 1) {
            return true;
        }

        if (!app_session_is_registered('app_logged_users_id')) {
            return true;
        }

        if ($app_module_path == 'users/2step_verification') {
            return true;
        }

        //skip for guest user
        if (\K::f3()->CFG_ENABLE_GUEST_LOGIN == 1 and \K::f3()->CFG_GUEST_LOGIN_USER == $app_user['id']) {
            return true;
        }

        if (!isset($two_step_verification_info['is_checked']) and !in_array(
                $app_module_path,
                ['users/2step_verification', 'users/login']
            )) {
            redirect_to('users/2step_verification');
        }
    }

    public static function send_code()
    {
        global $app_user, $app_users_cache, $two_step_verification_info;

        $code = $two_step_verification_info['code'] = two_step_verification . phprand(0, 9) . rand(0, 9) . rand(0, 9);

        switch (\K::f3()->CFG_2STEP_VERIFICATION_TYPE) {
            case 'email':
                $users_info_query = db_query(
                    "select * from app_entity_1 where id='" . db_input($app_user['id']) . "' and field_5=1"
                );
                if ($users_info = db_fetch_array($users_info_query) and isset($app_user['email'])) {
                    $options = [
                        'to' => $users_info['field_9'],
                        'to_name' => $app_users_cache[$users_info['id']]['name'],
                        'subject' => \K::f3()->TEXT_2STEP_VERIFICATION_EMAIL_SUBJECT,
                        'body' => sprintf(\K::f3()->TEXT_2STEP_VERIFICATION_EMAIL_BODY, $code),
                        'from' => \K::f3()->CFG_EMAIL_ADDRESS_FROM,
                        'from_name' => \K::f3()->TEXT_EMAIL_NAME_FROM,
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
                    $module_id = \K::f3()->CFG_2STEP_VERIFICATION_SMS_MODULE;
                    $phone = (isset(
                        $users_info['field_' . \K::f3()->CFG_2STEP_VERIFICATION_USER_PHONE]
                    ) ? $users_info['field_' . \K::f3()->CFG_2STEP_VERIFICATION_USER_PHONE] : '');

                    if (strlen($phone)) {
                        $message_text = \K::f3()->TEXT_2STEP_VERIFICATION_EMAIL_SUBJECT . '. ' . sprintf(
                                \K::f3()->TEXT_2STEP_VERIFICATION_EMAIL_BODY,
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