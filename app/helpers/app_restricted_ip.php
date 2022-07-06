<?php

namespace Helpers;

class App_restricted_ip
{
    static function is_enabled()
    {
        if (\K::$fw->CFG_RESTRICTED_BY_IP_ENABLE == true and strlen(\K::$fw->CFG_ALLOWED_IP_LIST)) {
            return true;
        } else {
            return false;
        }
    }

    static function verify()
    {
        if (self::is_enabled()) {
            if (!in_array($_SERVER['REMOTE_ADDR'], array_map('trim', explode(',', \K::$fw->CFG_ALLOWED_IP_LIST)))) {
                echo \K::$fw->TEXT_ACCESS_FORBIDDEN;
                exit();
            }
        }
    }
}