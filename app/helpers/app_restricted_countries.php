<?php

namespace Helpers;

class App_restricted_countries
{
    static function is_enabled()
    {
        if (\K::$fw->CFG_RESTRICTED_COUNTRIES_ENABLE == true and strlen(\K::$fw->CFG_ALLOWED_COUNTRIES_LIST)) {
            return true;
        } else {
            return false;
        }
    }

    static function verify()
    {
        if (self::is_enabled()) {
            if (!function_exists("geoip_country_code_by_addr")) {
                include("includes/libs/maxmind/src/geoip.inc");
            }

            $gi = geoip_open("includes/libs/maxmind/GeoIP.dat", GEOIP_STANDARD);

            $country_code = geoip_country_code_by_addr($gi, $_SERVER['REMOTE_ADDR']);

            geoip_close($gi);

            if (!in_array($country_code, array_map('trim', explode(',', \K::$fw->CFG_ALLOWED_COUNTRIES_LIST)))) {
                echo \K::$fw->TEXT_ACCESS_FORBIDDEN;
                exit();
            }
        }
    }
}