<?php

namespace Tools\FieldsTypes;

class Fieldtype_php_code
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_PHP_CODE_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[\K::$fw->TEXT_PHP_CODE][] = [
            'title' => '<div style="text-align:left; margin-bottom: 5px;">' . fields::get_available_fields_helper(
                    $_POST['entities_id'],
                    'fields_configuration_php_code'
                ) . '</div>',
            'name' => 'php_code',
            'type' => 'code',
            'params' => ['class' => 'form-control', 'mode' => 'php']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_DEBUG_MODE,
            'name' => 'debug_mode',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_FIELDTYPE_PHP_CODE_RUN_DYNAMIC_INFO
                ) . \K::$fw->TEXT_RUN_DYNAMIC,
            'name' => 'dynamic_query',
            'type' => 'checkbox'
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        return '';
    }

    public function process($options)
    {
        return '';
    }

    public function output($options)
    {
        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        if ($cfg->get('dynamic_query') == 1) {
            return self::run_code(
                $options['field']['entities_id'],
                $options['item']['id'],
                $options['field']['id'],
                $options['item']
            );
        } else {
            return $options['value'];
        }
    }

    public static function run($entities_id, $items_id, $item_info = false)
    {
        $forceCommit = \K::model()->forceCommit();

        foreach (\K::$fw->app_fields_cache[$entities_id] as $field_id => $field) {
            $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

            if ($field['type'] == 'fieldtype_php_code' and $cfg->get('dynamic_query') != 1) {
                $output_value = self::run_code($entities_id, $items_id, $field_id, $item_info);

                /*db_query(
                    "update app_entity_{$entities_id} set field_{$field_id}='" . db_input(
                        $output_value
                    ) . "' where id='" . db_input($items_id) . "'"
                );*/
                \K::model()->db_update(
                    'app_entity_' . $entities_id,
                    ['field_' . $field_id => $output_value],
                    ['id = ?', $items_id]
                );
            }
        }

        if ($forceCommit) {
            \K::model()->commit();
        }
    }

    public static function run_code($entities_id, $items_id, $field_id, $item_info = false)
    {
        $cfg = new \Models\Main\Fields_types_cfg(\K::$fw->app_fields_cache[$entities_id][$field_id]['configuration']);

        $is_dynamic_query = false;

        if (!$item_info) {
            $is_dynamic_query = true;

            $item_info = \K::model()->db_query_exec_one(
                "select e.* " . fieldtype_formula::prepare_query_select(
                    $entities_id,
                    ''
                ) . " from app_entity_" . $entities_id . " e where e.id = ?",
                $items_id
            );
            //$item_info = db_fetch_array($item_info_query);
        }

        \K::$fw->current_field_value = $item_info['field_' . $field_id];

        $fields_values = $item_info;

        if (\K::$fw->app_entities_cache[$entities_id]['parent_id'] > 0) {
            if (!isset(\K::$fw->parent_item_holder[$item_info['parent_item_id']])) {
                /*$parent_item_query = db_query(
                    "select * from app_entity_{\K::$fw->app_entities_cache[$entities_id]['parent_id']} where id={$item_info['parent_item_id']}"
                );*/

                $parent_item = \K::model()->db_fetch_one(
                    'app_entity_' . \K::$fw->app_entities_cache[$entities_id]['parent_id'], [
                        'id = ?',
                        $item_info['parent_item_id']
                    ]
                );

                \K::$fw->parent_item_holder[$item_info['parent_item_id']] = $parent_item;
            } else {
                $parent_item = \K::$fw->parent_item_holder[$item_info['parent_item_id']];
            }

            if ($parent_item) {
                foreach ($parent_item as $fields_id => $fields_value) {
                    if (strstr($fields_id, 'field_')) {
                        $fields_values[$fields_id] = $fields_value;
                    }
                }
            }
        }

        $php_code = $cfg->get('php_code');

        $php_code = str_replace('[current_user_id]', \K::$fw->app_user['id'], $php_code);

        //prepare values to replace
        foreach ($fields_values as $fields_id => $fields_value) {
            $fields_id = str_replace('field_', '', $fields_id);

            if (!strlen($fields_value)) {
                $fields_value = 0;
            } elseif (is_string($fields_value)) {
                $fields_value = "'" . addslashes($fields_value) . "'";
            }

            $php_code = str_replace('[' . $fields_id . ']', $fields_value, $php_code);
        }

        if ($cfg->get('debug_mode') == 1 and !$is_dynamic_query) {
            \Helpers\App::print_r($fields_values);
            \Helpers\App::print_r(htmlspecialchars($php_code));
        }

        if (!strlen($php_code)) {
            return '';
        }

        try {
            eval($php_code);
        } catch (\Error $e) {
            echo \Helpers\App::alert_error(\K::$fw->TEXT_ERROR . ' ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

        return ($output_value ?? '');
    }

    public function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $sql = reports::prepare_numeric_sql_filters($filters, $options['prefix']);

        if (count($sql) > 0) {
            $sql_query[] = implode(' and ', $sql);
        }

        return $sql_query;
    }
}