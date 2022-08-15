<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_nested_calculations
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_NESTED_CALCULATIONS_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $choices = [
            'COUNT' => \K::$fw->TEXT_FUNCTION_COUNT,
            'SUM' => \K::$fw->TEXT_FUNCTION_SUM,
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_FUNCTION,
            'name' => 'calc_function',
            'type' => 'dropdown',
            'choices' => $choices,
            'params' => ['class' => 'form-control input-xlarge']
        ];

        $choices = [
            'top_level' => \K::$fw->TEXT_ONLY_AT_THE_TOP_LEVEL,
            'all_tree' => \K::$fw->TEXT_ALL_OVER_TREE_BRANCH,
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_PERFORM_CALCULATION,
            'name' => 'calc_type',
            'type' => 'dropdown',
            'choices' => $choices,
            'params' => ['class' => 'form-control input-medium']
        ];

        $typeIn = \K::model()->quoteToString(
            [
                'fieldtype_input_numeric',
                'fieldtype_input_numeric_comments',
                'fieldtype_js_formula',
                'fieldtype_mysql_query',
                'fieldtype_php_code'
            ]
        );

        $choices = [];
        $fields_query = \Models\Main\Fields::get_query(
            \K::$fw->POST['entities_id'],
            " and f.type in (" . $typeIn . ")"
        );

        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $choices[$fields['id']] = $fields['name'];
        }

        $cfg[] = [
            'title' => \K::$fw->TEXT_FIELD,
            'name' => 'calc_field_id',
            'type' => 'dropdown',
            'choices' => $choices,
            'params' => ['class' => 'form-control input-xlarge'],
            'form_group' => ['form_display_rules' => 'fields_configuration_calc_function:SUM,MIN,MAX']
        ];

        $cfg[] = [
            'title' => \Helpers\App::tooltip_icon(\K::$fw->TEXT_NUMBER_FORMAT_INFO) . \K::$fw->TEXT_NUMBER_FORMAT,
            'name' => 'number_format',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small input-masked', 'data-mask' => '9/~/~'],
            'default' => \K::$fw->CFG_APP_NUMBER_FORMAT
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_PREFIX,
            'name' => 'prefix',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_SUFFIX,
            'name' => 'suffix',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        return $cfg;
    }

    public function process($options)
    {
        return $options['value'];
    }

    public function output($options)
    {
        //return non-formated value if export
        if (isset($options['is_export']) and !isset($options['is_print'])) {
            return $options['value'];
        }

        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        if (strlen($cfg->get('number_format')) > 0 and strlen($options['value']) > 0 and is_numeric(
                $options['value']
            )) {
            $format = explode('/', str_replace('*', '', $cfg->get('number_format')));

            $value = number_format($options['value'], $format[0], $format[1], $format[2]);
        } else {
            $value = $options['value'];
        }

        //add prefix and suffix
        return (strlen($value) ? $cfg->get('prefix') . $value . $cfg->get('suffix') : '');
    }

    public static function update_items_fields($entities_id, $item_id, $parent_id)
    {
        $forceCommit = \K::model()->forceCommit();

        foreach (\K::$fw->app_fields_cache[$entities_id] as $field) {
            if ($field['type'] == 'fieldtype_nested_calculations') {
                if ($parent_id != 0) {
                    $item_id = \Models\Main\Items\Tree_table::get_top_parent_item_id($entities_id, $parent_id);
                }

                $cfg = new \Tools\Settings($field['configuration']);

                $calc_function = $cfg->get('calc_function');
                $calc_field_id = $cfg->get('calc_field_id');
                $update_field_id = $field['id'];

                //stop calculation if field not exist
                if ($calc_function == 'SUM' and !isset(\K::$fw->app_fields_cache[$entities_id][$calc_field_id])) {
                    if ($forceCommit) {
                        \K::model()->commit();
                    }

                    return false;
                }

                switch ($calc_function) {
                    case 'SUM':
                        if ($cfg->get('calc_type') == 'top_level') {
                            $sum = self::calc_sum($entities_id, $item_id, $calc_field_id);

                            /*db_query(
                                "update app_entity_{$entities_id} set field_{$update_field_id}={$sum} where id={$item_id}"
                            );*/

                            \K::model()->db_update(
                                'app_entity_' . (int)$entities_id,
                                ['field_' . (int)$update_field_id => $sum],
                                [
                                    'id = ?',
                                    $item_id
                                ]
                            );
                        } else {
                            foreach (
                                \Models\Main\Items\Tree_table::get_items_tree($entities_id, $item_id, [$item_id]
                                ) as $update_item_id
                            ) {
                                $sum = self::calc_sum($entities_id, $update_item_id, $calc_field_id);

                                /*db_query(
                                    "update app_entity_{$entities_id} set field_{$update_field_id}={$sum} where id={$update_item_id}"
                                );*/

                                \K::model()->db_update(
                                    'app_entity_' . (int)$entities_id,
                                    ['field_' . (int)$update_field_id => $sum],
                                    [
                                        'id = ?',
                                        $update_item_id
                                    ]
                                );
                            }
                        }

                        break;
                    case 'COUNT':
                        if ($cfg->get('calc_type') == 'top_level') {
                            $sum = self::calc_count($entities_id, $item_id);

                            /*db_query(
                                "update app_entity_{$entities_id} set field_{$update_field_id}={$sum} where id={$item_id}"
                            );*/

                            \K::model()->db_update(
                                'app_entity_' . (int)$entities_id,
                                ['field_' . (int)$update_field_id => $sum],
                                [
                                    'id = ?',
                                    $item_id
                                ]
                            );
                        } else {
                            foreach (
                                \Models\Main\Items\Tree_table::get_items_tree($entities_id, $item_id, [$item_id]
                                ) as $update_item_id
                            ) {
                                $sum = self::calc_count($entities_id, $update_item_id);

                                /*db_query(
                                    "update app_entity_{$entities_id} set field_{$update_field_id}={$sum} where id={$update_item_id}"
                                );*/

                                \K::model()->db_update(
                                    'app_entity_' . (int)$entities_id,
                                    ['field_' . (int)$update_field_id => $sum],
                                    [
                                        'id = ?',
                                        $update_item_id
                                    ]
                                );
                            }
                        }

                        break;
                }
            }
        }

        if ($forceCommit) {
            \K::model()->commit();
        }
    }

    public static function calc_sum($entities_id, $item_id, $calc_field_id, $sum = 0)
    {
        /*$items_query = db_query(
            "select id, field_{$calc_field_id} from app_entity_{$entities_id} where parent_id={$item_id}"
        );*/

        $items_query = \K::model()->db_fetch('app_entity_' . (int)$entities_id, [
            'parent_id = ?',
            $item_id
        ], [], 'id,field_' . (int)$calc_field_id);

        //while ($items = db_fetch_array($items_query)) {
        foreach ($items_query as $items) {
            $items = $items->cast();

            if (isset($items['field_' . $calc_field_id]) and strlen($items['field_' . $calc_field_id])) {
                $sum += (float)$items['field_' . $calc_field_id];
            }

            $sum = self::calc_sum($entities_id, $items['id'], $calc_field_id, $sum);
        }

        return $sum;
    }

    public static function calc_count($entities_id, $item_id, $count = 0)
    {
        //$items_query = db_query("select id from app_entity_{$entities_id} where parent_id={$item_id}");

        $items_query = \K::model()->db_fetch('app_entity_' . (int)$entities_id, [
            'parent_id = ?',
            $item_id
        ], [], 'id');

        //while ($items = db_fetch_array($items_query)) {
        foreach ($items_query as $items) {
            $items = $items->cast();

            $count = $count + 1;

            $count = self::calc_count($entities_id, $items['id'], $count);
        }

        return $count;
    }
}