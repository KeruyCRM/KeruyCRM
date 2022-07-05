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
        app_restricted_countries::verify();
        app_restricted_ip::verify();

        if (app_session_is_registered('app_logged_users_id')) {
            $app_module_action = 'logoff';
        }

        $app_layout = 'login_layout.php';
    }

    public function login()
    {
    }
}