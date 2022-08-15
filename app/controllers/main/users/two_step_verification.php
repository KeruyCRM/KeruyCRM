<?php

namespace Controllers\Main\Users;

class Two_step_verification extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken('main/users/login');

        \K::$fw->app_layout = 'public_layout.php';

        //check security settings if they are enabled
        \Helpers\App_restricted_countries::verify();
        \Helpers\App_restricted_ip::verify();
    }

    public function index()
    {
        if (!\K::app_session_is_registered('app_logged_users_id') or \K::$fw->CFG_2STEP_VERIFICATION_ENABLED != 1) {
            \Helpers\Urls::redirect_to('main/users/login');
        }

        //check if is checked
        if (isset(\K::$fw->two_step_verification_info['is_checked'])) {
            \Helpers\Urls::redirect_to('main/dashboard');
        }

        if (!isset(\K::$fw->two_step_verification_info['code'])) {
            \Models\Main\Users\Two_step_verification::send_code();
        }

        switch (\K::$fw->CFG_2STEP_VERIFICATION_TYPE) {
            case 'email':
                $email = \K::$fw->app_user['email'];
                if (strlen($email) < 15) {
                    $email = substr_replace($email, str_repeat('*', strlen($email) - 5), -5);
                } else {
                    $email = substr_replace($email, str_repeat('*', strlen($email) - 10), 5, -5);
                }

                \K::$fw->page_title = \K::$fw->TEXT_CODE_FROM_EMAIL;
                \K::$fw->page_body = sprintf(\K::$fw->TEXT_CODE_FROM_EMAIL_INFO, $email);
                break;
            case 'sms':
                $phone = \K::$fw->app_user['fields']['field_' . \K::$fw->CFG_2STEP_VERIFICATION_USER_PHONE];
                \K::$fw->page_title = \K::$fw->TEXT_CODE_FROM_SMS;
                \K::$fw->page_body = sprintf(
                    \K::$fw->TEXT_CODE_FROM_SMS_INFO,
                    substr_replace($phone, str_repeat('*', strlen($phone) - 7), 5, -2)
                );
                break;
        }

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'two_step_verification.php';
        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function check()
    {
        if (\K::$fw->VERB == 'POST') {
            if (\K::$fw->two_step_verification_info['code'] == \K::$fw->{'POST.code'}) {
                \Models\Main\Users\Two_step_verification::approve();
            } else {
                \K::flash()->addMessage(\K::$fw->TEXT_INCORRECT_CODE, 'error');
                \Helpers\Urls::redirect_to('main/users/two_step_verification');
            }
        } else {
            \Helpers\Urls::redirect_to('main/users/login');
        }
    }
}