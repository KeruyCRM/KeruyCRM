<?php

class K
{
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

    public static function sessionExists($key, &$val = null)
    {
        return self::fw()->exists('SESSION.' . $key, $val);
    }

    public static function sessionGet($key, $args = null)
    {
        return self::fw()->get('SESSION.' . $key, $args);
    }

    public static function sessionSet($key, $val, $initRef = false, $ttl = 0)
    {
        self::fw()->set('SESSION.' . $key, $val, $ttl);
        if ($initRef) {
            self::fw()->refSync($key, self::fw()->{'SESSION.' . $key});
        }
    }

    public static function sessionClear($key)
    {
        self::fw()->clear('SESSION.' . $key);
    }

    public static function cookieExists($key, &$val = null)
    {
        return self::fw()->exists('COOKIE.' . $key, $val);
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