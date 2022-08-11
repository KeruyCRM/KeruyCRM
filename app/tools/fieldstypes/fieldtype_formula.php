<?php

namespace Tools\FieldsTypes;

class Fieldtype_formula
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_FORMULA_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_FORMULA . \Models\Main\Fields::get_available_fields_helper(
                    \K::fw()->get('POST.entities_id'),
                    'fields_configuration_formula'
                ),
            'name' => 'formula',
            'type' => 'code_small',
            'tooltip_icon' => \K::$fw->TEXT_FORMULA_TIP_USAGE,
            'tooltip' => \K::$fw->TEXT_FORMULA_TIP,
            'params' => ['class' => 'form-control code', 'mode' => 'sql']
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
            'title' => \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_CALCULATE_AVERAGE_VALUE_INFO
                ) . \K::$fw->TEXT_CALCULATE_AVERAGE_VALUE,
            'name' => 'calculate_average',
            'type' => 'checkbox'
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY_TIP
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
        global $sql_query_having;

        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $sql = \Models\Main\Reports\Reports::prepare_numeric_sql_filters($filters, '');

        if (count($sql) > 0) {
            $sql_query_having[$options['entities_id']][] = implode(' and ', $sql);
        }

        return $sql_query;
    }

    /*
     * to save server load we check if formula needed in listing
     */

    public static function check_formula_query_needed($formula_fields_id, $entities_id, $check_needed)
    {
        //global $mysql_formula_reports_info_holder;

        $check_formula_needed = false;
        $reports_info = [];

        //check if formula field is in listing
        if (isset($check_needed['reports_id'])) {
            if (!isset(\K::$fw->mysql_formula_reports_info_holder[$check_needed['reports_id']])) {
                if (\Helpers\App::is_mobile()) {
                    if (\Models\Main\Listing_types::has_mobile($entities_id)) {
                        $reports_info['listing_type'] = 'mobile';
                    }
                }

                if (!isset($reports_info)) {
                    /*$reports_info_query = db_query(
                        "select id, entities_id, fields_in_listing, listing_type from app_reports where id='" . $check_needed['reports_id'] . "'"
                    );
                    $reports_info = db_fetch_array($reports_info_query);*/
                    $reports_info = \K::model()->db_fetch_one('app_reports', [
                        'id = ?',
                        $check_needed['reports_id']
                    ], [], 'id,entities_id,fields_in_listing,listing_type');
                }

                if (!strlen($reports_info['listing_type'])) {
                    $reports_info['listing_type'] = \Models\Main\Listing_types::get_default($entities_id);
                }

                //prepare fields in listing for List and Grid
                if (in_array($reports_info['listing_type'], ['list', 'grid', 'mobile'])) {
                    $fields_in_listing = [];
                    /*$listing_type_query = db_query(
                        "select id from app_listing_types where entities_id='" . $entities_id . "' and type='" . $reports_info['listing_type'] . "' and is_active=1"
                    );*/

                    $listing_type = \K::model()->db_fetch_one('app_listing_types', [
                        'entities_id = ? and type = ? and is_active = 1',
                        $entities_id,
                        $reports_info['listing_type']
                    ], [], 'id');
                    //if ($listing_type = db_fetch_array($listing_type_query)) {
                    if ($listing_type) {
                        /*$listing_sections_query = db_query(
                            "select fields from app_listing_sections where listing_types_id={$listing_type['id']} order by sort_order, name"
                        );*/
                        $listing_sections_query = \K::model()->db_fetch('app_listing_sections', [
                            'listing_types_id = ?',
                            $listing_type['id']
                        ], ['order' => 'sort_order,name'], 'fields');

                        //while ($listing_sections = db_fetch_array($listing_sections_query)) {
                        foreach ($listing_sections_query as $listing_sections) {
                            $listing_sections = $listing_sections->cast();

                            if (strlen($listing_sections['fields'])) {
                                $fields_in_listing = array_merge(
                                    $fields_in_listing,
                                    explode(',', $listing_sections['fields'])
                                );
                            }
                        }
                    }

                    $reports_info['fields_in_listing'] = implode(',', $fields_in_listing);
                } elseif (in_array($reports_info['listing_type'], ['tree_table'])) {
                    $fields_in_listing = [];
                    /*$listing_type_query = db_query(
                        "select settings from app_listing_types where entities_id='" . $entities_id . "' and type='" . $reports_info['listing_type'] . "'"
                    );*/

                    $listing_type = \K::model()->db_fetch_one('app_listing_types', [
                        'entities_id = ? and type = ?',
                        $entities_id,
                        $reports_info['listing_type']
                    ], [], 'settings');

                    //if ($listing_type = db_fetch_array($listing_type_query)) {
                    if ($listing_type) {
                        $settings = new \Tools\Settings($listing_type['settings']);

                        if (is_array($settings->get('fields_in_listing'))) {
                            $reports_info['fields_in_listing'] = implode(',', $settings->get('fields_in_listing'));
                        }
                    }
                }

                \K::$fw->mysql_formula_reports_info_holder[$check_needed['reports_id']] = $reports_info;
            } else {
                $reports_info = \K::$fw->mysql_formula_reports_info_holder[$check_needed['reports_id']];
            }
        }

        $text_pattern_where_sql = '';

        //check custom listing fields

        if (isset($check_needed['fields_in_query'])) {
            if (in_array($formula_fields_id, explode(',', $check_needed['fields_in_query']))) {
                return true;
            } else {
                return false;
            }
        } elseif (isset($check_needed['fields_in_listing'])) {
            if (in_array($formula_fields_id, explode(',', $check_needed['fields_in_listing']))) {
                $check_formula_needed = true;
            }

            if (strlen($check_needed['fields_in_listing'])) {
                $text_pattern_where_sql = " and id in (" . $check_needed['fields_in_listing'] . ")";
            }
        } //check reports settings
        elseif (strlen($reports_info['fields_in_listing'])) {
            if (in_array($formula_fields_id, explode(',', $reports_info['fields_in_listing']))) {
                $check_formula_needed = true;
            }

            $text_pattern_where_sql = " and id in (" . $reports_info['fields_in_listing'] . ")";
        } //check default listing settings
        else {
            /*$check_query = db_query(
                "select id from app_fields where id='" . $formula_fields_id . "' and listing_status=1"
            );*/
            $check = \K::model()->db_fetch_one('app_fields', [
                'id = ? and listing_status = 1',
                $formula_fields_id
            ], [], 'id');

            //if ($check = db_fetch_array($check_query)) {
            if ($check) {
                $check_formula_needed = true;
            }

            $text_pattern_where_sql = " and listing_status = 1";
        }

        //check if formula used in filters
        if (!$check_formula_needed and isset($check_needed['reports_id'])) {
            /*$check_query = db_query(
                "select count(*) as total from app_reports_filters where reports_id='" . $check_needed['reports_id'] . "' and fields_id='" . $formula_fields_id . "'"
            );*/

            $total = \K::model()->db_fetch_count('app_reports_filters', [
                'reports_id = ? and fields_id = ?',
                $check_needed['reports_id'],
                $formula_fields_id
            ]);
            //$check = db_fetch_array($check_query);

            if ($total > 0) {
                $check_formula_needed = true;
            }
        }

        //check if text pattern using formulas
        if (!$check_formula_needed and strlen($text_pattern_where_sql)) {
            /*$fields_query = db_query(
                "select configuration from app_fields where entities_id='" . $entities_id . "' {$text_pattern_where_sql} and type='fieldtype_text_pattern'"
            );*/

            $fields_query = \K::model()->db_fetch('app_fields', [
                'entities_id = ? ' . $text_pattern_where_sql . ' and type = ?',
                $entities_id,
                'fieldtype_text_pattern'
            ], [], 'configuration');

            //while ($fields = db_fetch_array($fields_query)) {
            foreach ($fields_query as $fields) {
                $fields = $fields->cast();

                $cfg = new \Models\Main\Fields_types_cfg($fields['configuration']);
                $pattern = $cfg->get('pattern');

                if (strstr($pattern, '[' . $formula_fields_id . ']')) {
                    $check_formula_needed = true;
                }
            }
        }

        //check mysql query
        if (!$check_formula_needed) {
            /*$fields_query = db_query(
                "select configuration from app_fields where entities_id='" . $entities_id . "' and type='fieldtype_items_by_query'"
            );*/
            $fields_query = \K::model()->db_fetch('app_fields', [
                'entities_id = ? and type = ?',
                $entities_id,
                'fieldtype_items_by_query'
            ], [], 'configuration');

            //while ($fields = db_fetch_array($fields_query)) {
            foreach ($fields_query as $fields) {
                $fields = $fields->cast();

                $cfg = new \Models\Main\Fields_types_cfg($fields['configuration']);

                if (strstr($cfg->get('where_query'), '[' . $formula_fields_id . ']')) {
                    $check_formula_needed = true;
                }
            }
        }

        return $check_formula_needed;
    }

    /**
     *  function to prepare sql
     *  by default function return string with formulas query
     *  $prepare_field_sum with ture return fields sum (using in graph report)
     *  $listing_sql_query_select as array return list of sql query in array (using in listing total calculation)
     */
    public static function prepare_query_select(
        $entities_id,
        $listing_sql_query_select = '',
        $prepare_field_sum = false,
        $check_needed = false
    ) {
        //global $app_not_formula_fields_cache, $app_formula_fields_cache, $app_entities_cache, $app_currencies_cache, $app_fields_cache, $app_user, $app_global_vars;

        //get available fields for formula
        $available_fields = [];
        if (isset(\K::$fw->app_not_formula_fields_cache[$entities_id])) {
            $available_fields = \K::$fw->app_not_formula_fields_cache[$entities_id];
        }

        //get formulas    
        if (isset(\K::$fw->app_formula_fields_cache[$entities_id])) {
            $formulas_fields = [];

            foreach (\K::$fw->app_formula_fields_cache[$entities_id] as $fields) {
                $cfg = \Models\Main\Fields_types::parse_configuration($fields['configuration']);

                if (strlen($cfg['formula'])) {
                    $formulas_fields[$fields['id']] = '(' . $cfg['formula'] . ')';
                }
            }

            foreach (\K::$fw->app_formula_fields_cache[$entities_id] as $fields) {
                $cfg = new \Models\Main\Fields_types_cfg($fields['configuration']);

                $formula = $cfg->get('formula');

                //check if formula needed in query
                if ($check_needed) {
                    if (!self::check_formula_query_needed($fields['id'], $entities_id, $check_needed)) {
                        continue;
                    }
                }

                if (strlen($formula) > 0) {
                    //prepare formula fields
                    $formula = self::prepare_formula_fields($formulas_fields, $formula);

                    //prepare fields
                    foreach ($available_fields as $fields_id) {
                        //handler mysql query field type in formula
                        if (strstr(
                                $formula,
                                '[' . $fields_id . ']'
                            ) and \K::$fw->app_fields_cache[$entities_id][$fields_id]['type'] == 'fieldtype_mysql_query') {
                            $formula = str_replace(
                                '[' . $fields_id . ']',
                                \Tools\FieldsTypes\Fieldtype_mysql_query::prepare_query(
                                    \K::$fw->app_fields_cache[$entities_id][$fields_id],
                                    'e',
                                    true
                                ),
                                $formula
                            );
                        } elseif (strstr(
                                $formula,
                                '[' . $fields_id . ']'
                            ) and \K::$fw->app_fields_cache[$entities_id][$fields_id]['type'] == 'fieldtype_days_difference') {
                            $formula = str_replace(
                                '[' . $fields_id . ']',
                                \Tools\FieldsTypes\Fieldtype_days_difference::prepare_query(
                                    \K::$fw->app_fields_cache[$entities_id][$fields_id],
                                    'e',
                                    true
                                ),
                                $formula
                            );
                        } elseif (strstr(
                                $formula,
                                '[' . $fields_id . ']'
                            ) and \K::$fw->app_fields_cache[$entities_id][$fields_id]['type'] == 'fieldtype_hours_difference') {
                            $formula = str_replace(
                                '[' . $fields_id . ']',
                                \Tools\FieldsTypes\Fieldtype_hours_difference::prepare_query(
                                    \K::$fw->app_fields_cache[$entities_id][$fields_id],
                                    'e',
                                    true
                                ),
                                $formula
                            );
                        } elseif (strstr(
                                $formula,
                                '[' . $fields_id . ']'
                            ) and \K::$fw->app_fields_cache[$entities_id][$fields_id]['type'] == 'fieldtype_years_difference') {
                            $formula = str_replace(
                                '[' . $fields_id . ']',
                                \Tools\FieldsTypes\Fieldtype_years_difference::prepare_query(
                                    \K::$fw->app_fields_cache[$entities_id][$fields_id],
                                    'e',
                                    true
                                ),
                                $formula
                            );
                        } elseif (strstr(
                                $formula,
                                '[' . $fields_id . ']'
                            ) and \K::$fw->app_fields_cache[$entities_id][$fields_id]['type'] == 'fieldtype_months_difference') {
                            $formula = str_replace(
                                '[' . $fields_id . ']',
                                \Tools\FieldsTypes\Fieldtype_months_difference::prepare_query(
                                    \K::$fw->app_fields_cache[$entities_id][$fields_id],
                                    'e',
                                    true
                                ),
                                $formula
                            );
                        } else {
                            $formula = str_replace('[' . $fields_id . ']', 'e.field_' . $fields_id, $formula);
                        }
                    }

                    //handle get_vallue()
                    $formula = self::prepare_choices_get_value_function($entities_id, $formula);

                    //prepare parent items values
                    $formula = self::prepare_parent_entity_item_value($entities_id, $formula);

                    //prepare [TODAY]

                    /*$formula = str_replace('[TODAY]', get_date_timestamp(date('Y-m-d')), $formula);

                    $formula = str_replace('[id]', 'e.id', $formula);
                    $formula = str_replace('[date_added]', 'e.date_added', $formula);
                    $formula = str_replace('[date_updated]', 'e.date_updated', $formula);
                    $formula = str_replace('[created_by]', 'e.created_by', $formula);
                    $formula = str_replace('[parent_item_id]', 'e.parent_item_id', $formula);
                    $formula = str_replace('[current_user_id]', \K::$fw->app_user['id'], $formula);*/

                    $formula = str_replace(
                        [
                            '[TODAY]',
                            '[id]',
                            '[date_added]',
                            '[date_updated]',
                            '[created_by]',
                            '[parent_item_id]',
                            '[current_user_id]'
                        ],
                        [
                            \Helpers\App::get_date_timestamp(date('Y-m-d')),
                            'e.id',
                            'e.date_added',
                            'e.date_updated',
                            'e.created_by',
                            'e.parent_item_id',
                            \K::$fw->app_user['id']
                        ],
                        $formula
                    );

                    //prepare [currency code]
                    if (\Helpers\App::is_ext_installed() and \K::fw()->exists('app_currencies_cache')) {
                        foreach (\K::$fw->app_currencies_cache as $currency) {
                            $formula = str_replace('[' . $currency['code'] . ']', $currency['value'], $formula);
                        }
                    }

                    if (strstr($formula, '{') and class_exists('functions')) {
                        $formula = functions::prepare_formula_query($entities_id, $formula);
                    }

                    if (!strstr($formula, '[') and !strstr($formula, '{')) {
                        if ($prepare_field_sum) {
                            $listing_sql_query_select .= ", sum(" . $formula . ") as sum_field_" . $fields['id'] . " ";
                        } elseif (is_array($listing_sql_query_select)) {
                            $listing_sql_query_select[] = "(" . $formula . ") as field_" . $fields['id'];
                        } else {
                            $listing_sql_query_select .= ", (" . $formula . ") as field_" . $fields['id'];
                        }
                    } else {
                        echo '<div class="alert alert-danger">' . sprintf(
                                \K::$fw->TEXT_ERROR_FORMULA_CALCULATION,
                                \K::$fw->app_entities_cache[$entities_id]['name'],
                                $fields['name'],
                                $fields['id'],
                                $cfg->get('formula')
                            ) . '</div>';
                    }
                }
            }
        }

        //prepare mysql query field type in main query
        $listing_sql_query_select = \Tools\FieldsTypes\Fieldtype_mysql_query::prepare_query_select(
            $entities_id,
            $listing_sql_query_select
        );

        //prepare days diff query field type in main query
        $listing_sql_query_select = \Tools\FieldsTypes\Fieldtype_days_difference::prepare_query_select(
            $entities_id,
            $listing_sql_query_select
        );

        //prepare hours diff query field type in main query
        $listing_sql_query_select = \Tools\FieldsTypes\Fieldtype_hours_difference::prepare_query_select(
            $entities_id,
            $listing_sql_query_select
        );

        //prepare years diff query field type in main query
        $listing_sql_query_select = \Tools\FieldsTypes\Fieldtype_years_difference::prepare_query_select(
            $entities_id,
            $listing_sql_query_select
        );

        //prepare months diff query field type in main query
        $listing_sql_query_select = \Tools\FieldsTypes\Fieldtype_months_difference::prepare_query_select(
            $entities_id,
            $listing_sql_query_select
        );

        //prepare encrypted fields
        $listing_sql_query_select = \Tools\FieldsTypes\Fieldtype_input_encrypted::prepare_query_select(
            $entities_id,
            $listing_sql_query_select
        );

        return \K::app_global_vars()->apply_to_text($listing_sql_query_select);
    }

    public static function prepare_parent_entity_item_value($entities_id, $listing_sql_query_select)
    {
        global $app_entities_cache;

        if (preg_match_all("/parent_entity_item_value\((\d+),(\d+)\)/", $listing_sql_query_select, $matches)) {
            foreach ($matches[1] as $key => $use_parent_entity_id) {
                $use_field_id = $matches[2][$key];

                //get parent entities
                $parents = \Models\Main\Entities::get_parents($entities_id);

                //print_rr($parents);

                if (in_array($use_parent_entity_id, $parents)) {
                    //set parents for query
                    $use_parents = [];
                    foreach ($parents as $v) {
                        if ($use_parent_entity_id == $v) {
                            break;
                        }

                        $use_parents[] = $v;
                    }

                    $parents = array_reverse($use_parents);

                    //print_rr($parents);

                    $count = 0;
                    $slq = "(select field_{$use_field_id} from app_entity_{$use_parent_entity_id} where id = ";

                    //if no parents means use next level parent
                    if (!count($parents)) {
                        $slq .= " e.parent_item_id";
                    }

                    //build query for all next parents
                    foreach ($parents as $parent_entity_id) {
                        if ($app_entities_cache[$entities_id]['parent_id'] == $parent_entity_id) {
                            $slq .= "(select parent_item_id from app_entity_{$parent_entity_id} where id=e.parent_item_id";
                        } else {
                            $slq .= "(select parent_item_id from app_entity_{$parent_entity_id} where id=";
                        }

                        $count++;
                    }

                    $slq .= str_repeat(')', $count + 1);

                    $listing_sql_query_select = str_replace($matches[0][$key], $slq, $listing_sql_query_select);
                }
                //echo $slq. '<br>';
            }
        }

        return $listing_sql_query_select;
    }

    public static function prepare_formula_fields($formulas_fields, $formula)
    {
        $check_count = 0;

        do {
            $check_count++;

            foreach ($formulas_fields as $fields_id => $formula_text) {
                $formula = str_replace('[' . $fields_id . ']', $formula_text, $formula);
            }

            $check = false;

            foreach ($formulas_fields as $fields_id => $formula_text) {
                if (strstr($formula, '[' . $fields_id . ']')) {
                    $check = true;
                }
            }
        } while ($check == true and $check_count < 200);

        return $formula;
    }

    public static function prepare_choices_get_value_function($entities_id, $formula, $prefix = 'e')
    {
        //global $app_fields_cache;

        if (preg_match_all("/get_value\([^)]*\)/", $formula, $matches)) {
            foreach ($matches[0] as $get_value_function) {
                $field_id = str_replace(['get_value(' . $prefix . '.field_', ')'], '', $get_value_function);

                /*$field_query = db_query(
                    "select type,configuration from app_fields where id='" . db_input($field_id) . "'"
                );*/
                $field = \K::model()->db_fetch_one('app_fields', [
                    'id = ?',
                    $field_id
                ], [], 'type,configuration');

                //if ($field = db_fetch_array($field_query)) {
                if ($field) {
                    $cfg = new \Tools\Settings($field['configuration']);

                    if (($list_id = $cfg->get('use_global_list')) > 0) {
                        switch ($field['type']) {
                            case 'fieldtype_dropdown':
                            case 'fieldtype_radioboxes':
                                $formula = str_replace(
                                    "get_value({$prefix}.field_" . $field_id,
                                    "(select fcv.value from app_global_lists_choices fcv where fcv.lists_id ={$list_id} and fcv.id = {$prefix}.field_" . $field_id,
                                    $formula
                                );
                                break;
                            default:
                                $to_replace_str = str_replace(
                                        "get_value(",
                                        "(select sum(fcv.value) from app_global_lists_choices fcv where fcv.lists_id ={$list_id} and find_in_set(fcv.id,",
                                        $get_value_function
                                    ) . ")";
                                $formula = str_replace($get_value_function, $to_replace_str, $formula);
                                break;
                        }
                    } else {
                        switch ($field['type']) {
                            case 'fieldtype_dropdown':
                            case 'fieldtype_radioboxes':
                                $formula = str_replace(
                                    "get_value({$prefix}.field_" . $field_id,
                                    "(select fcv.value from app_fields_choices fcv where fcv.id = {$prefix}.field_" . $field_id,
                                    $formula
                                );
                                break;
                            default:
                                $to_replace_str = str_replace(
                                        "get_value(",
                                        "(select sum(fcv.value) from app_fields_choices fcv where find_in_set(fcv.id,",
                                        $get_value_function
                                    ) . ")";
                                $formula = str_replace($get_value_function, $to_replace_str, $formula);
                                break;
                        }
                    }
                }
            }
        }

        if (preg_match_all("/entity_item_value\((\d+),(\d+)\)/", $formula, $matches)) {
            //print_rr($matches);

            foreach ($matches[1] as $key => $entity_field_id) {
                $select_field_id = $matches[2][$key];

                if (!isset(\K::$fw->app_fields_cache[$entities_id][$entity_field_id])) {
                    continue;
                }

                $cfg = new \Tools\Settings(\K::$fw->app_fields_cache[$entities_id][$entity_field_id]['configuration']);
                $select_entity_id = (int)$cfg->get('entity_id');

                if ($select_entity_id == 0) {
                    continue;
                }

                switch ($cfg->get('display_as')) {
                    case 'dropdown':
                        $sql = "(select field_{$select_field_id} from app_entity_{$select_entity_id} where id=e.field_{$entity_field_id})";
                        break;
                    default:
                        $sql = "(select sum(field_{$select_field_id}) from app_entity_{$select_entity_id} where find_in_set(id,e.field_{$entity_field_id}))";
                        break;
                }

                $formula = str_replace($matches[0][$key], $sql, $formula);
            }
        }

        return $formula;
    }
}