<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Models\Main;

class Configuration
{
    static function set($k, $value)
    {
        \K::model()->db_perform('app_configuration', [
            'configuration_value' => $value,
            'configuration_name' => $k
        ], ['configuration_name = ?', $k]);

        /*$cfq_query = db_query("select * from app_configuration where configuration_name='" . $k . "'");
        if (!$cfq = db_fetch_array($cfq_query)) {
            db_perform('app_configuration', ['configuration_value' => $value, 'configuration_name' => $k]);
        } else {
            db_perform(
                'app_configuration',
                ['configuration_value' => $value],
                'update',
                "configuration_name='" . $k . "'"
            );
        }*/
    }
}