<?php

class K
{// 'K' !!!
    public static $fw;

    public static function keruy()
    {
        if (is_null(self::$fw)) {
            self::$fw = self::fw();
        }
        return self::$fw;
    }

    public static function fw()
    {
        return \Base::instance();
    }

    public static function model()
    {
        return \Model::instance();
    }

    public static function cache()
    {
        return \Cache::instance();
    }

    public static function audit()
    {
        return \Audit::instance();
    }

    public static function matrix()
    {
        return \Matrix::instance();
    }

    public static function utf()
    {
        return \UTF::instance();
    }

    public static function flash()
    {
        return \Flash::instance();
    }

    public static function security()
    {
        return \Helpers\Security::instance();
    }

    public static function app_global_vars()
    {
        return \Tools\GlobalVars::instance();
    }

    public static function users_cfg()
    {
        return \Models\Users\Users_cfg::instance();
    }

    public static function app_session_is_registered($key)
    {
        return self::fw()->exists('SESSION.' . $key);
    }

    public static function app_session_register($key, $val = null)
    {
        if (self::fw()->exists($key)) {
            self::fw()->SESSION[$key] = &self::fw()->{$key};
        } else {
            self::fw()->set('SESSION.' . $key, $val);
        }
    }

    public static function app_session_unregister($key)
    {
        self::fw()->clear('SESSION.' . $key);
    }

    public static function app_session_table_reset()
    {
        //db_query("delete from app_sessions where expiry < '" . strtotime("-1 day") . "'");
    }

    public static function sessionGet($key, $args = null)
    {
        return self::fw()->get('SESSION.' . $key, $args);
    }

    public static function cookieExists($key)
    {
        return self::fw()->exists('COOKIE.' . $key);
    }

    public static function cookieGet($key, $args = null)
    {
        return self::fw()->get('COOKIE.' . $key, $args);
    }

    public static function cookieSet($key, $val, $ttl = 0)
    {
        return self::fw()->set('COOKIE.' . $key, $val, $ttl);
    }

    public static function cookieClear($key)
    {
        self::fw()->clear('COOKIE.' . $key);
    }

    public static function reroute($url, $module = 'module/', $permanent = false, $die = true)
    {
        return self::fw()->reroute('/' . $module . $url, $permanent, $die);
    }
}