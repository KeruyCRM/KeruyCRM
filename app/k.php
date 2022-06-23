<?php

class K
{
    public static function f3()
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
        return self::f3()->exists('SESSION.' . $key, $val);
    }

    public static function sessionGet($key, $args = null)
    {
        return self::f3()->get('SESSION.' . $key, $args);
    }

    public static function sessionSet($key, $val, $ttl = 0)
    {
        return self::f3()->set('SESSION.' . $key, $val, $ttl);
    }

    public static function sessionClear($key)
    {
        self::f3()->clear('SESSION.' . $key);
    }

    public static function cookieExists($key, &$val = null)
    {
        return self::f3()->exists('COOKIE.' . $key, $val);
    }

    public static function cookieGet($key, $args = null)
    {
        return self::f3()->get('COOKIE.' . $key, $args);
    }

    public static function cookieSet($key, $val, $ttl = 0)
    {
        return self::f3()->set('COOKIE.' . $key, $val, $ttl);
    }

    public static function cookieClear($key)
    {
        self::f3()->clear('COOKIE.' . $key);
    }
}