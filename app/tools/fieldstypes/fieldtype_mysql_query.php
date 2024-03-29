<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_mysql_query
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_MYSQL_QUERY_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_FIELDTYPE_MYSQL_QUERY_DYNAMIC_QUERY_INFO
                ) . \K::$fw->TEXT_FIELDTYPE_MYSQL_QUERY_DYNAMIC_QUERY,
            'name' => 'dynamic_query',
            'type' => 'checkbox'
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_FIELDTYPE_MYSQL_QUERY_SELECT_ENTITY,
            'name' => 'entity_id',
            'tooltip_icon' => \K::$fw->TEXT_FIELDTYPE_MYSQL_QUERY_SELECT_ENTITY_TOOLTIP,
            'type' => 'dropdown',
            'choices' => \Models\Main\Entities::get_choices(),
            'params' => ['class' => 'form-control input-xlarge']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_FIELDTYPE_MYSQL_QUERY_SELECT_QUERY,
            'name' => 'select_query',
            'type' => 'textarea',
            'tooltip_icon' => \K::$fw->TEXT_FIELDTYPE_MYSQL_QUERY_SELECT_QUERY_TIP,
            'params' => ['class' => 'form-control textarea-small code required']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_FIELDTYPE_MYSQL_QUERY_WHERE_QUERY,
            'name' => 'where_query',
            'type' => 'textarea',
            'tooltip_icon' => \K::$fw->TEXT_FIELDTYPE_MYSQL_QUERY_WHERE_QUERY_TIP,
            'params' => ['class' => 'form-control textarea-small code required']
        ];

        $cfg[] = [
            'title' => \Helpers\App::tooltip_icon(\K::$fw->TEXT_NUMBER_FORMAT_INFO) . \K::$fw->TEXT_NUMBER_FORMAT,
            'name' => 'number_format',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small input-masked', 'data-mask' => '9/~/~'],
            'default' => \K::$fw->CFG_APP_NUMBER_FORMAT
        ];

        $cfg[] = [
            'title' => \Helpers\App::tooltip_icon(\K::$fw->TEXT_CALCULATE_TOTALS_INFO) . \K::$fw->TEXT_CALCULATE_TOTALS,
            'name' => 'calculate_totals',
            'type' => 'checkbox'
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_CALCULATE_AVERAGE_VALUE,
            'name' => 'calculate_average',
            'type' => 'checkbox'
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

    public function render($field, $obj, $params = [])
    {
        return '<p class="form-control-static">' . $obj['field_' . $field['id']] . '</p>' . \Helpers\Html::input_hidden_tag(
                'fields[' . $field['id'] . ']',
                $obj['field_' . $field['id']]
            );
    }

    public function process($options)
    {
        return $options['value'];
    }

    public function output($options)
    {
        return \Models\Main\Fields_types::outputFormula($options);
    }

    public function reports_query($options)
    {
        $cfg = new \Models\Main\Fields_types_cfg(
            \K::$fw->app_fields_cache[$options['entities_id']][$options['filters']['fields_id']]['configuration']
        );

        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $sql = \Models\Main\Reports\Reports::prepare_numeric_sql_filters($filters, '');

        if (count($sql) > 0 and $cfg->get('dynamic_query') == 1) {
            \K::$fw->sql_query_having[$options['entities_id']][] = implode(' and ', $sql);
        } elseif (count($sql) > 0) {
            $sql_query[] = implode(' and ', $sql);
        }

        return $sql_query;
    }

    public static function get_fields_cache()
    {
        $cache = [];
        $fields_query = \K::model()->db_fetch(
            'app_fields', ['type in (?)', 'fieldtype_mysql_query']
        );
        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $fields = $fields->cast();

            $cache[$fields['entities_id']][] = $fields;
        }

        return $cache;
    }

    public static function prepare_query_select($entities_id, $listing_sql_query_select, $prefix = 'e')
    {
        if (isset(\K::$fw->app_mysql_query_fields_cache[$entities_id])) {
            foreach (\K::$fw->app_mysql_query_fields_cache[$entities_id] as $fields) {
                $cfg = new \Models\Main\Fields_types_cfg($fields['configuration']);

                //skip query if not dynamic
                if ($cfg->get('dynamic_query') != 1 and \K::$fw->fieldtype_mysql_query_force != true) {
                    continue;
                }

                //array to calculate totals in listing
                if (is_array($listing_sql_query_select)) {
                    $listing_sql_query_select[] = self::prepare_query($fields, $prefix);
                } else {
                    $listing_sql_query_select .= ',' . self::prepare_query($fields, $prefix);
                }
            }
        }

        return $listing_sql_query_select;
    }

    public static function prepare_query($fields, $prefix = 'e', $single_select = false)
    {
        $cfg = new \Models\Main\Fields_types_cfg($fields['configuration']);

        //skip query if not dynamic
        if ($cfg->get('dynamic_query') != 1 and \K::$fw->fieldtype_mysql_query_force != true) {
            return $prefix . '.field_' . (int)$fields['id'];
        }

        //single select to include directly in formula
        if ($single_select) {
            $mysql_query = "(select " . $cfg->get('select_query') . " from app_entity_" . (int)$cfg->get(
                    'entity_id'
                ) . " msq where " . $cfg->get('where_query') . " limit 1)";
        } else {
            $mysql_query = "IFNULL((select " . $cfg->get('select_query') . " from app_entity_" . (int)$cfg->get(
                    'entity_id'
                ) . " msq where " . $cfg->get('where_query') . " limit 1),0) as field_" . (int)$fields['id'];
        }

        //prepare formulas
        $formulas_fields = [];

        if (isset(\K::$fw->app_formula_fields_cache[$cfg->get('entity_id')])) {
            foreach (\K::$fw->app_formula_fields_cache[$cfg->get('entity_id')] as $formula_field) {
                $formula_cfg = \Models\Main\Fields_types::parse_configuration($formula_field['configuration']);

                if (strlen($formula_cfg['formula'])) {
                    $formulas_fields[$formula_field['id']] = '(' . $formula_cfg['formula'] . ')';
                }
            }
        }

        $mysql_query = \Tools\FieldsTypes\Fieldtype_formula::prepare_formula_fields($formulas_fields, $mysql_query);

        //prepare [TODAY]
        $mysql_query = str_replace('[TODAY]', \Helpers\App::get_date_timestamp(date('Y-m-d')), $mysql_query);
        $mysql_query = str_replace('[current_user_id]', \K::$fw->app_user['id'], $mysql_query);

        $entities_id = $cfg->get('entity_id');

        //prepare fields entity query
        foreach (\K::$fw->app_not_formula_fields_cache[$cfg->get('entity_id')] as $fields_id) {
            $fields_type = isset(
                \K::$fw->app_fields_cache[$cfg->get(
                    'entity_id'
                )][$fields_id]['type']
            ) ? \K::$fw->app_fields_cache[$cfg->get('entity_id')][$fields_id]['type'] : '';
            if (in_array(
                $fields_type,
                ['fieldtype_input_numeric', 'fieldtype_input_numeric_comments', 'fieldtype_js_formula']
            )) {
                $mysql_query = str_replace(
                    '[' . $fields_id . ']',
                    '(msq.field_' . (int)$fields_id . '+0)',
                    $mysql_query
                );
            } elseif (strstr(
                    $mysql_query,
                    '[' . $fields_id . ']'
                ) and \K::$fw->app_fields_cache[$entities_id][$fields_id]['type'] == 'fieldtype_days_difference') {
                $mysql_query = str_replace(
                    '[' . $fields_id . ']',
                    \Tools\FieldsTypes\Fieldtype_days_difference::prepare_query(
                        \K::$fw->app_fields_cache[$entities_id][$fields_id],
                        'msq',
                        true
                    ),
                    $mysql_query
                );
            } elseif (strstr(
                    $mysql_query,
                    '[' . $fields_id . ']'
                ) and \K::$fw->app_fields_cache[$entities_id][$fields_id]['type'] == 'fieldtype_hours_difference') {
                $mysql_query = str_replace(
                    '[' . $fields_id . ']',
                    \Tools\FieldsTypes\Fieldtype_hours_difference::prepare_query(
                        \K::$fw->app_fields_cache[$entities_id][$fields_id],
                        'msq',
                        true
                    ),
                    $mysql_query
                );
            } elseif (strstr(
                    $mysql_query,
                    '[' . $fields_id . ']'
                ) and \K::$fw->app_fields_cache[$entities_id][$fields_id]['type'] == 'fieldtype_years_difference') {
                $mysql_query = str_replace(
                    '[' . $fields_id . ']',
                    \Tools\FieldsTypes\Fieldtype_years_difference::prepare_query(
                        \K::$fw->app_fields_cache[$entities_id][$fields_id],
                        'msq',
                        true
                    ),
                    $mysql_query
                );
            } elseif (strstr(
                    $mysql_query,
                    '[' . $fields_id . ']'
                ) and \K::$fw->app_fields_cache[$entities_id][$fields_id]['type'] == 'fieldtype_months_difference') {
                $mysql_query = str_replace(
                    '[' . $fields_id . ']',
                    \Tools\FieldsTypes\Fieldtype_months_difference::prepare_query(
                        \K::$fw->app_fields_cache[$entities_id][$fields_id],
                        'msq',
                        true
                    ),
                    $mysql_query
                );
            } else {
                $mysql_query = str_replace('[' . $fields_id . ']', 'msq.field_' . (int)$fields_id, $mysql_query);
            }
        }

        //handle get_value()
        $mysql_query = \Tools\FieldsTypes\Fieldtype_formula::prepare_choices_get_value_function(
            $cfg->get('entity_id'),
            $mysql_query,
            'msq'
        );

        //handle functions in ext
        if (strstr($mysql_query, '{') and \Helpers\App::is_ext_installed()) {
            $mysql_query = \Models\Main\Ext\Functions::prepare_formula_query(
                $cfg->get('entity_id'),
                $mysql_query,
                100,
                'msq'
            );
        }

        //prepare fields for current entity
        foreach (\K::$fw->app_not_formula_fields_cache[$fields['entities_id']] as $fields_id) {
            $fields_type = \K::$fw->app_fields_cache[$fields['entities_id']][$fields_id]['type'] ?? '';
            if (in_array(
                $fields_type,
                ['fieldtype_input_numeric', 'fieldtype_input_numeric_comments', 'fieldtype_js_formula']
            )) {
                $mysql_query = str_replace(
                    '[' . $fields_id . ']',
                    '(' . $prefix . '.field_' . (int)$fields_id . '+0)',
                    $mysql_query
                );
            } else {
                $mysql_query = str_replace('[' . $fields_id . ']', $prefix . '.field_' . (int)$fields_id, $mysql_query);
            }
        }

        return $mysql_query;
    }

    //function to update item if there are any not dynamic query
    public static function update_items_fields($entities_id, $items_id, $item_info = false)
    {
        $update_fields = [];

        if (isset(\K::$fw->app_mysql_query_fields_cache[$entities_id])) {
            foreach (\K::$fw->app_mysql_query_fields_cache[$entities_id] as $fields) {
                $cfg = new \Models\Main\Fields_types_cfg($fields['configuration']);

                if ($cfg->get('dynamic_query') != 1) {
                    $update_fields[] = $fields['id'];
                }
            }
        }

        if (count($update_fields)) {
            if (!$item_info) {
                $item_info = \K::model()->db_query_exec_one(
                    'select e.* ' . \Tools\FieldsTypes\Fieldtype_formula::prepare_query_select(
                        $entities_id,
                        ''
                    ) . ' from app_entity_' . (int)$entities_id . ' e where e.id = ?',
                    $items_id
                );
                //$item_info = $item_info_query[0] ?? '';
            }

            $forceCommit = \K::model()->forceCommit();

            foreach ($update_fields as $fields_id) {
                /*db_query(
                    "update app_entity_{$entities_id} set field_{$fields_id}='" . $item_info['field_' . $fields_id] . "' where id='" . db_input(
                        $items_id
                    ) . "'"
                );*/

                \K::model()->db_update('app_entity_' . (int)$entities_id, [
                    'field_' . (int)$fields_id => $item_info['field_' . (int)$fields_id]
                ], ['id = ?', $items_id]);
            }

            if ($forceCommit) {
                \K::model()->commit();
            }
        }
    }
}