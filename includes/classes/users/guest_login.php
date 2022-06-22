<?php

class guest_login
{
    static function is_enabled()
    {
        return CFG_ENABLE_GUEST_LOGIN == 1 and CFG_GUEST_LOGIN_USER > 0;
    }

    static function is_guest()
    {
        global $app_user, $app_previously_logged_user;

        return CFG_ENABLE_GUEST_LOGIN == 1 and CFG_GUEST_LOGIN_USER == $app_user['id'] and $app_previously_logged_user == 0;
    }
}
