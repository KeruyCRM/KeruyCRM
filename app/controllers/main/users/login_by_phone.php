<?php

namespace Controllers\Main\Users;

class Login_by_phone extends \Controller
{
    private $app_layout = 'login_layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken('main/users/login_by_phone');

        if (\K::$fw->CFG_2STEP_VERIFICATION_ENABLED != 1 or \K::$fw->CFG_LOGIN_BY_PHONE_NUMBER != 1 or \K::$fw->CFG_2STEP_VERIFICATION_TYPE != 'sms' or !isset(\K::$fw->app_fields_cache[1][\K::$fw->CFG_2STEP_VERIFICATION_USER_PHONE])) {
            \Helpers\Urls::redirect_to('main/users/login');
        }

        //check security settings if they are enabled
        \Helpers\App_restricted_countries::verify();
        \Helpers\App_restricted_ip::verify();

        if (\K::app_session_is_registered('app_logged_users_id')) {
            \Helpers\Urls::redirect_to('main/users/login/logoff', '', true);
        }
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'login_by_phone.php';

        echo \K::view()->render($this->app_layout);
    }

    public function login()
    {
        if (\K::$fw->VERB == 'POST') {
            //check recaptcha
            if (\Helpers\App_recaptcha::is_enabled() and !\Helpers\App_recaptcha::verify()) {
                \K::flash()->addMessage(\K::$fw->TEXT_RECAPTCHA_VERIFY_ROBOT, 'error');
                \Helpers\Urls::redirect_to('main/users/login_by_phone');
            }

            //check phone
            if (!strlen(preg_replace('/\D/', '', \K::$fw->{'POST.phone'}))) {
                \K::flash()->addMessage(\K::$fw->TEXT_USER_IS_NOT_FOUND, 'error');
                \Helpers\Urls::redirect_to('main/users/login_by_phone');
            }

            //check if user exist with this phone
            /*$user_query = db_query(
                "select id from app_entity_1 where length(field_" . \K::$fw->CFG_2STEP_VERIFICATION_USER_PHONE . ")>0 and keruycrm_regex_replace('[^0-9]','',field_" . \K::$fw->CFG_2STEP_VERIFICATION_USER_PHONE . ") = '" . db_input(
                    preg_replace('/\D/', '', $_POST['phone'])
                ) . "'"
            );*/

            $user = \K::model()->db_fetch_one('app_entity_1', [
                "length(field_" . \K::$fw->CFG_2STEP_VERIFICATION_USER_PHONE . ") > 0 and keruycrm_regex_replace('[^0-9]','',field_" . \K::$fw->CFG_2STEP_VERIFICATION_USER_PHONE . ") = ?",
                preg_replace('/\D/', '', \K::$fw->{'POST.phone'})
            ], [], 'id');

            if ($user) {
                \K::app_session_register('app_logged_users_id', $user['id']);

                \Helpers\Urls::redirect_to('main/users/two_step_verification');
            } else {
                \K::flash()->addMessage(\K::$fw->TEXT_USER_IS_NOT_FOUND, 'error');
                \Helpers\Urls::redirect_to('main/users/login_by_phone');
            }
        } else {
            \Helpers\Urls::redirect_to('main/users/login');
        }
    }
}