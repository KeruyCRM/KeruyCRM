<?php

namespace Controllers\Main\Users;

class Login extends \Controller
{
    private $app_layout = 'login_layout.php';

    public function __construct()
    {
        parent::__construct();

        //force ldap login only
        if (\K::$fw->CFG_LDAP_USE == 1 and \K::$fw->CFG_USE_LDAP_LOGIN_ONLY == 1 and \K::$fw->app_module_action != 'logoff') {
            \Helpers\Urls::redirect_to('main/users/ldap_login');
        }

        //check security settings if they are enabled
        \Helpers\App_restricted_countries::verify();
        \Helpers\App_restricted_ip::verify();
    }

    public function index()
    {
        if (\K::app_session_is_registered('app_logged_users_id')) {
            $this->logoff();
        }

        echo \K::view()->render($this->app_layout);
    }

    public function login()
    {
        if (\K::$fw->VERB == 'POST') {
            if (\K::$fw->CFG_ENABLE_SOCIAL_LOGIN == 2) {
                \Helpers\Urls::redirect_to('main/users/login');
            }

            //check recaptcha
            if (\Helpers\App_recaptcha::is_enabled()) {
                if (!\Helpers\App_recaptcha::verify()) {
                    \K::flash()->add(\K::$fw->TEXT_RECAPTCHA_VERIFY_ROBOT, 'error');
                    \Helpers\Urls::redirect_to('main/users/login');
                }
            }

            \Models\Main\Users\Users::login(
                \K::$fw->{'POST.username'},
                \K::$fw->{'POST.password'},
                (\K::fw()->exists('POST.remember_me') ? 1 : 0)
            );
        } else {
            \Helpers\Urls::redirect_to('main/users/login');
        }
    }

    public function logoff()
    {
        \K::app_session_unregister('app_logged_users_id');
        \K::app_session_unregister('app_current_version');
        \K::app_session_unregister('two_step_verification_info');
        \K::app_session_unregister('app_email_verification_code');
        \K::app_session_unregister('app_session_token');

        \K::cookieClear('app_stay_logged');
        \K::cookieClear('app_remember_user');
        \K::cookieClear('app_remember_pass');
        \K::cookieClear('izoColorPickerColors');

        \Helpers\Urls::redirect_to('main/users/login');
    }
}