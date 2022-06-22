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
}