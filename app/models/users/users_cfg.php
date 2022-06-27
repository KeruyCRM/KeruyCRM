<?php

namespace Models\Users;

class Users_cfg
{
    public $cfg;

    public function __construct()
    {
        if (!isset(\K::$fw->app_user['id'])) {
            return false;
        }

        $cfg_query = \K::model()->db_fetch('app_users_configuration', ['users_id = ?', \K::$fw->app_user['id']]);

        //while ($v = db_fetch_array($cfg_query)) {
        foreach ($cfg_query as $v) {
            $v = $v->cast();

            $this->cfg[$v['configuration_name']] = $v['configuration_value'];
        }
    }

    public function get($key, $default = '')
    {
        if (isset($this->cfg[$key])) {
            return $this->cfg[$key];
        } else {
            return $default;
        }
    }

    public function set($key, $value)
    {
        if (strlen($key) > 0) {
            \K::model()->db_perform(
                'app_users_configuration',
                [
                    'configuration_name' => $key,
                    'configuration_value' => trim($value),
                    'users_id' => \K::$fw->app_user['id']
                ], '',
                ['users_id = ? and configuration_name = ?', \K::$fw->app_user['id'], $key]
            );
        }
    }

    public static function get_value_by_users_id($users_id, $key, $default = '')
    {
        $cfg_query = \K::model()->db_fetch_one(
            'app_users_configuration',
            ['users_id = ? and configuration_name = ?', $users_id, $key]
        );

        if ($cfg = $cfg_query->cast()) {
            return $cfg['configuration_value'];
        } else {
            return $default;
        }
    }
}