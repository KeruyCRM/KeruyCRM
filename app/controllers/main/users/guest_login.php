<?php

namespace Controllers\Main\Users;

class Guest_login extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        //check security settings if they are enabled
        \Helpers\App_restricted_countries::verify();
        \Helpers\App_restricted_ip::verify();
    }

    public function index()
    {
        if (\Models\Main\Users\Guest_login::is_enabled()) {
            \K::app_session_register('app_logged_users_id', \K::$fw->CFG_GUEST_LOGIN_USER);

            \Helpers\Urls::redirect_to('main/dashboard/dashboard');
        } else {
            \Helpers\Urls::redirect_to('main/users/login');
        }
    }
}