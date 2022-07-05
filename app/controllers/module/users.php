<?php

namespace Controllers\Module;

class Users extends \Controller
{
    public function __construct()
    {
        parent::__construct();

        //force ldap login only
        if (\K::$fw->CFG_LDAP_USE == 1 and \K::$fw->CFG_USE_LDAP_LOGIN_ONLY == 1 and \K::$fw->app_module_action != 'logoff') {
            \Helpers\Urls::redirect_to('module/users/ldap_login');
        }

        //check security settings if they are enabled
        \Helpers\App_restricted_countries::verify();
        \Helpers\App_restricted_ip::verify();

        if (\K::app_session_is_registered('app_logged_users_id')) {
            $app_module_action = 'logoff';
        }

        $app_layout = 'login_layout.php';
    }

    public function login()
    {
        if (\K::$fw->CFG_ENABLE_SOCIAL_LOGIN == 2) {
            \Helpers\Urls::redirect_to('module/users/login');
        }

        //chck form token
        //app_check_form_token('users/login');

        //check reaptcha
        if (app_recaptcha::is_enabled()) {
            if (!app_recaptcha::verify()) {
                $alerts->add(TEXT_RECAPTCHA_VERIFY_ROBOT, 'error');
                redirect_to('users/login');
            }
        }

        users::login($_POST['username'], $_POST['password'], (isset($_POST['remember_me']) ? 1 : 0));
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

        redirect_to('users/login');
    }
}