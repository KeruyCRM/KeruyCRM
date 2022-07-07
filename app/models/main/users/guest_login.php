<?php

namespace Models\Main\Users;

class Guest_login
{
    public static function is_enabled()
    {
        return \K::$fw->CFG_ENABLE_GUEST_LOGIN == 1 and \K::$fw->CFG_GUEST_LOGIN_USER > 0;
    }

    public static function is_guest()
    {
        return \K::$fw->CFG_ENABLE_GUEST_LOGIN == 1
            and \K::$fw->CFG_GUEST_LOGIN_USER == \K::$fw->app_user['id']
            and \K::$fw->app_previously_logged_user == 0;
    }
}