<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Models\Main;

class Entities_cfg
{
    public $cfg;

    public $entities_id;

    public function __construct($entities_id)
    {
        $this->entities_id = $entities_id;

        //$info_query = db_fetch_all('app_entities_configuration', "entities_id='" . db_input($this->entities_id) . "'");
        $info_query = \K::model()->db_fetch('app_entities_configuration', [
            'entities_id = ?',
            $this->entities_id
        ], [], 'configuration_name,configuration_value');

        //while ($info = db_fetch_array($info_query)) {
        foreach ($info_query as $info) {
            $info = $info->cast();

            $this->cfg[$info['configuration_name']] = $info['configuration_value'];
        }
    }

    public function get($key, $default = '')
    {
        return $this->cfg[$key] ?? $default;
    }

    public function set($key, $value)
    {
        if (strlen($key) > 0) {
            $value = (is_array($value) ? implode(',', $value) : $value);

            \K::model()->db_perform(
                'app_entities_configuration',
                [
                    'configuration_value' => $value,
                    'configuration_name' => $key,
                    'entities_id' => $this->entities_id
                ],
                [
                    "configuration_name = ? and entities_id= ?",
                    $key,
                    $this->entities_id
                ]
            );
        }
    }
}