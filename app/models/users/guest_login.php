<?php

namespace Models\Users;

class Guest_login
{
    public static function is_enabled()
    {
        return \K::f3()->CFG_ENABLE_GUEST_LOGIN == 1 and \K::f3()->CFG_GUEST_LOGIN_USER > 0;
    }

    public static function is_guest()
    {
        return \K::f3()->CFG_ENABLE_GUEST_LOGIN == 1
            and \K::f3()->CFG_GUEST_LOGIN_USER == \K::f3()->app_user['id']
            and \K::f3()->app_previously_logged_user == 0;
    }
}