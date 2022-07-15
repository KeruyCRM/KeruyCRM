<?php

namespace Models\Main;

class Entities_cfg
{
    public $cfg;

    public $entities_id;

    function __construct($entities_id)
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

    function get($key, $default = '')
    {
        return $this->cfg[$key] ?? $default;
    }

    function set($key, $value)
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
            /*$cfq_query = db_query(
                "select * from app_entities_configuration where configuration_name='" . db_input(
                    $key
                ) . "' and entities_id='" . db_input($this->entities_id) . "'"
            );

            if (!$cfq = db_fetch_array($cfq_query)) {
                db_perform(
                    'app_entities_configuration',
                    ['configuration_value' => $value, 'configuration_name' => $key, 'entities_id' => $this->entities_id]
                );
            } else {
                db_perform(
                    'app_entities_configuration',
                    ['configuration_value' => $value],
                    'update',
                    "configuration_name='" . db_input($key) . "' and entities_id='" . db_input($this->entities_id) . "'"
                );
            }*/
        }
    }
}
