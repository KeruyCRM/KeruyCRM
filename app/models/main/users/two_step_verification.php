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

        if (\K::$fw->app_module_path == 'users/two_step_verification') {
            return true;
        }

        //skip for guest user
        if (\K::$fw->CFG_ENABLE_GUEST_LOGIN == 1 and \K::$fw->CFG_GUEST_LOGIN_USER == \K::$fw->app_user['id']) {
            return true;
        }

        if (!isset(\K::$fw->two_step_verification_info['is_checked']) and !in_array(
                \K::$fw->app_module_path,
                ['users/two_step_verification', 'users/login']
            )) {
            \Helpers\Urls::redirect_to('main/users/two_step_verification');
        }
    }

    public static function send_code()
    {
        //global $app_user, $app_users_cache, $two_step_verification_info;

        $code = '';
        try {
            $code = \K::$fw->two_step_verification_info['code'] = str_pad(
                (string)random_int(0, (int)str_repeat('9', \K::$fw->CFG_VERIFICATION_CODE_LENGTH)),
                \K::$fw->CFG_VERIFICATION_CODE_LENGTH,
                '0',
                STR_PAD_LEFT
            );
        } catch (\Exception $e) {
        }

        switch (\K::$fw->CFG_2STEP_VERIFICATION_TYPE) {
            case 'email':
                /*$users_info_query = db_query(
                    "select * from app_entity_1 where id='" . db_input(\K::$fw->app_user['id']) . "' and field_5=1"
                );*/

                $users_info = \K::model()->db_fetch_one('app_entity_1', [
                    'id = ? and field_5 = 1',
                    \K::$fw->app_user['id']
                ], [], 'id,field_9');

                if ($users_info and isset(\K::$fw->app_user['email'])) {
                    $options = [
                        'to' => $users_info['field_9'],
                        'to_name' => \K::$fw->app_users_cache[$users_info['id']]['name'],
                        'subject' => \K::$fw->TEXT_2STEP_VERIFICATION_EMAIL_SUBJECT,
                        'body' => sprintf(\K::$fw->TEXT_2STEP_VERIFICATION_EMAIL_BODY, $code),
                        'from' => \K::$fw->CFG_EMAIL_ADDRESS_FROM,
                        'from_name' => \K::$fw->TEXT_EMAIL_NAME_FROM,
                        'send_directly' => true,
                    ];

                    \Models\Main\Users\Users::send_email($options);
                }
                break;

            case 'sms':
                /*$users_info_query = db_query(
                    "select * from app_entity_1 where id='" . db_input(\K::$fw->app_user['id']) . "' and field_5=1"
                );*/

                $users_info = \K::model()->db_fetch_one('app_entity_1', [
                    'id = ? and field_5 = 1',
                    \K::$fw->app_user['id']
                ]);

                if ($users_info and isset(\K::$fw->app_user['email'])) {
                    $module_id = \K::$fw->CFG_2STEP_VERIFICATION_SMS_MODULE;
                    $phone = ($users_info['field_' . \K::$fw->CFG_2STEP_VERIFICATION_USER_PHONE] ?? '');

                    if (strlen($phone)) {
                        $message_text = \K::$fw->TEXT_2STEP_VERIFICATION_EMAIL_SUBJECT . '. ' . sprintf(
                                \K::$fw->TEXT_2STEP_VERIFICATION_EMAIL_BODY,
                                $code
                            );
                        \Models\Ext\Sms::send_by_module($module_id, $phone, $message_text);
                    } else {
                        //TODO ???
                        self::approve();
                    }
                }
                break;
        }
    }

    public static function approve()
    {
        //global $two_step_verification_info, $app_user;

        \K::$fw->two_step_verification_info['is_checked'] = true;
        unset(\K::$fw->two_step_verification_info['code']);

        \Models\Main\Users\Users_login_log::success(\K::$fw->app_user['username'], \K::$fw->app_user['id']);

        if (\K::cookieExists('app_login_redirect_to')) {
            \Helpers\Urls::redirect_to(\K::cookieGet('app_login_redirect_to'));
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}