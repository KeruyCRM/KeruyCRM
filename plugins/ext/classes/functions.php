<?php

class functions
{

    static function get_cache()
    {
        $cache = [];
        $functions_query = db_query("select f.* from app_ext_functions f");
        while ($functions = db_fetch_array($functions_query)) {
            $cache[$functions['id']] = $functions;
        }

        return $cache;
    }

    static public function get_choices()
    {
        $choices = [];
        $choices['SUM'] = TEXT_EXT_FUNCTION_SUM;
        $choices['COUNT'] = TEXT_EXT_FUNCTION_COUNT;
        $choices['MIN'] = TEXT_EXT_FUNCTION_MIN;
        $choices['MAX'] = TEXT_EXT_FUNCTION_MAX;
        $choices['SELECT'] = TEXT_EXT_FUNCTION_SELECT;

        return $choices;
    }

    static public function prepare_formula_query($entities_id, $formula, $table_prefix = 100, $e_prefix = 'e')
    {
        global $app_functions_cache;

        foreach ($app_functions_cache as $functions) {
            if (strstr($formula, '{' . $functions['id'] . '}')) {
                $formula = str_replace(
                    '{' . $functions['id'] . '}',
                    self::prepare_function_query($entities_id, $functions['id'], 0, $table_prefix, $e_prefix),
                    $formula
                );
            }

            if (preg_match_all('/{(\d+):(\d+)}/', $formula, $matches)) {
                //echo '<br><br>';
                //print_r($matches[1]);
                //echo '<br><br>';

                foreach ($matches[1] as $matches_key => $functions_id) {
                    $perform_field_id = $matches[2][$matches_key];
                    $formula = str_replace(
                        '{' . $functions_id . ':' . $perform_field_id . '}',
                        self::prepare_function_query(
                            $entities_id,
                            $functions_id,
                            $perform_field_id,
                            $table_prefix,
                            $e_prefix
                        ),
                        $formula
                    );
                }
            }
        }

        return $formula;
    }

    static public function prepare_function_query(
        $entities_id,
        $functions_id,
        $perform_field_id = 0,
        $table_prefix = 100,
        $e_prefix = 'e'
    ) {
        global $app_functions_cache, $reports_filters_query_holder;

        if (isset($app_functions_cache[$functions_id])) {
            $function_info = $app_functions_cache[$functions_id];

            switch ($function_info['functions_name']) {
                case 'COUNT':
                    $sql = "select count(*) from app_entity_" . $function_info['entities_id'] . " func{$table_prefix} where func{$table_prefix}.id>0 ";
                    break;
                case 'SUM':
                case 'MIN':
                case 'MAX':
                    $sql = "select " . $function_info['functions_name'] . "(" . self::prepare_formula_in_function_query(
                            $function_info['entities_id'],
                            $function_info['functions_formula'],
                            $table_prefix
                        ) . "+0) from app_entity_" . $function_info['entities_id'] . " func{$table_prefix} where func{$table_prefix}.id>0 ";
                    break;
                case 'SELECT':
                    $sql = "select (" . self::prepare_formula_in_function_query(
                            $function_info['entities_id'],
                            $function_info['functions_formula'],
                            $table_prefix
                        ) . ") from app_entity_" . $function_info['entities_id'] . " func{$table_prefix} where func{$table_prefix}.id>0 ";
                    break;
            }

            //
            $sql = self::add_field_query(
                $entities_id,
                $function_info['entities_id'],
                $perform_field_id,
                $sql,
                $table_prefix,
                $e_prefix
            );

            //add filters query

            $reports_filters_query_holder_id = $function_info['reports_id'] . 'func' . $table_prefix . '_' . $perform_field_id;

            if (!isset($reports_filters_query_holder[$reports_filters_query_holder_id])) {
                $sql = $reports_filters_query_holder[$reports_filters_query_holder_id] = reports::add_filters_query(
                    $function_info['reports_id'],
                    $sql,
                    'func' . $table_prefix
                );
            } else {
                $sql = $reports_filters_query_holder[$reports_filters_query_holder_id];
            }

            //get paret entities
            $parent_entities = entities::get_parents($function_info['entities_id']);

            //check if current entity in parent entities and prepare parent entities query
            if (in_array($entities_id, $parent_entities)) {
                $sql = self::prepare_parent_entities_query(
                    $parent_entities,
                    $entities_id,
                    $sql,
                    $table_prefix,
                    $e_prefix
                );
            }

            if ($function_info['functions_name'] == 'SELECT') {
                $entities_info = db_find('app_entities', $entities_id);

                //select value from paretn entity
                if ($entities_info['parent_id'] == $function_info['entities_id'] and $entities_info['parent_id'] > 0) {
                    $e_prefix = ($table_prefix == 100 ? $e_prefix : 'func' . ($table_prefix + 1));

                    $sql .= " and func{$table_prefix}.id={$e_prefix}.parent_item_id limit 1";
                } else {
                    $sql .= " order by func{$table_prefix}.id desc limit 1";
                }
            }

            return ' (' . $sql . ') ';
        } else {
            return '{' . $functions_id . '}';
        }
    }

    static public function add_field_query(
        $entities_id,
        $function_entities_id,
        $perform_field_id,
        $sql,
        $table_prefix,
        $e_prefix
    ) {
        if ($perform_field_id > 0) {
            $field_query = db_query(
                "select * from app_fields where entities_id='" . $entities_id . "' and id='" . $perform_field_id . "'"
            );
            if ($field = db_fetch_array($field_query)) {
                $e_prefix = ($table_prefix == 100 ? $e_prefix : 'func' . ($table_prefix + 1));

                switch ($field['type']) {
                    case 'fieldtype_related_records':

                        $cfg = new fields_types_cfg($field['configuration']);
                        $related_entities_id = $cfg->get('entity_id');

                        $table_info = related_records::get_related_items_table_name(
                            $entities_id,
                            $cfg->get('entity_id')
                        );

                        $related_items_sql = "select ri.entity_" . $cfg->get(
                                'entity_id'
                            ) . $table_info['sufix'] . "_items_id from " . $table_info['table_name'] . " ri where ri.entity_" . $entities_id . "_items_id={$e_prefix}.id";
                        $sql .= " and (func{$table_prefix}.id in (" . $related_items_sql . ")";

                        if (strlen($table_info['sufix']) > 0) {
                            $related_items_sql = "select ri.entity_" . $cfg->get(
                                    'entity_id'
                                ) . "_items_id from " . $table_info['table_name'] . " ri where ri.entity_" . $entities_id . $table_info['sufix'] . "_items_id={$e_prefix}.id";
                            $sql .= " or func{$table_prefix}.id in (" . $related_items_sql . ")";
                        }

                        $sql .= ")";

                        //echo $sql;

                        break;
                    case 'fieldtype_entity':
                    case 'fieldtype_entity_ajax':
                    case 'fieldtype_users':
                    case 'fieldtype_users_ajax':
                        $related_items_sql = "select cv.value from app_entity_" . $entities_id . "_values cv where cv.fields_id='" . db_input(
                                $field['id']
                            ) . "' and cv.items_id={$e_prefix}.id";
                        $sql .= " and func{$table_prefix}.id in (" . $related_items_sql . ")";
                        break;
                }
            }
        }

        return $sql;
    }

    static public function prepare_formula_in_function_query($entities_id, $formula, $table_prefix)
    {
        global $app_not_formula_fields_cache, $app_formula_fields_cache, $app_fields_cache, $app_user, $app_currencies_cache;

        if (strlen($formula) == 0) {
            return '*';
        }


        $available_fields = [];
        if (isset($app_not_formula_fields_cache[$entities_id])) {
            $available_fields = $app_not_formula_fields_cache[$entities_id];
        }

        //get formulas
        $formulas_fields = [];
        if (isset($app_formula_fields_cache[$entities_id])) {
            foreach ($app_formula_fields_cache[$entities_id] as $fields) {
                $cfg = fields_types::parse_configuration($fields['configuration']);

                if (strlen($cfg['formula'])) {
                    $formulas_fields[$fields['id']] = '(' . $cfg['formula'] . ')';
                }
            }
        }

        //prepare formula fields
        $formula = fieldtype_formula::prepare_formula_fields($formulas_fields, $formula);

        foreach ($available_fields as $fields_id) {
            //hander mysql qeury field type in formula
            $field_type = (isset($app_fields_cache[$entities_id][$fields_id]['type']) ? $app_fields_cache[$entities_id][$fields_id]['type'] : '');
            if ($field_type == 'fieldtype_mysql_query') {
                $formula = str_replace(
                    '[' . $fields_id . ']',
                    fieldtype_mysql_query::prepare_query(
                        $app_fields_cache[$entities_id][$fields_id],
                        'func' . $table_prefix,
                        true
                    ),
                    $formula
                );
            } elseif ($field_type == 'fieldtype_days_difference') {
                $formula = str_replace(
                    '[' . $fields_id . ']',
                    fieldtype_days_difference::prepare_query(
                        $app_fields_cache[$entities_id][$fields_id],
                        'func' . $table_prefix,
                        true
                    ),
                    $formula
                );
            } elseif ($field_type == 'fieldtype_hours_difference') {
                $formula = str_replace(
                    '[' . $fields_id . ']',
                    fieldtype_hours_difference::prepare_query(
                        $app_fields_cache[$entities_id][$fields_id],
                        'func' . $table_prefix,
                        true
                    ),
                    $formula
                );
            } elseif ($field_type == 'fieldtype_years_difference') {
                $formula = str_replace(
                    '[' . $fields_id . ']',
                    fieldtype_years_difference::prepare_query(
                        $app_fields_cache[$entities_id][$fields_id],
                        'func' . $table_prefix,
                        true
                    ),
                    $formula
                );
            } elseif ($field_type == 'fieldtype_months_difference') {
                $formula = str_replace(
                    '[' . $fields_id . ']',
                    fieldtype_months_difference::prepare_query(
                        $app_fields_cache[$entities_id][$fields_id],
                        'func' . $table_prefix,
                        true
                    ),
                    $formula
                );
            } else {
                $formula = str_replace(
                    '[' . $fields_id . ']',
                    'func' . $table_prefix . '.field_' . $fields_id,
                    $formula
                );
            }
        }

        //prepare [TODAY]
        $formula = str_replace('[TODAY]', get_date_timestamp(date('Y-m-d')), $formula);

        $formula = str_replace('[id]', 'func' . $table_prefix . '.id', $formula);
        $formula = str_replace('[date_added]', 'func' . $table_prefix . '.date_added', $formula);
        $formula = str_replace('[created_by]', 'func' . $table_prefix . '.created_by', $formula);
        $formula = str_replace('[parent_item_id]', 'func' . $table_prefix . '.parent_item_id', $formula);
        $formula = str_replace('[current_user_id]', $app_user['id'], $formula);

        if (isset($app_currencies_cache)) {
            foreach ($app_currencies_cache as $currecny) {
                $formula = str_replace('[' . $currecny['code'] . ']', $currecny['value'], $formula);
            }
        }

        //handle get_vallue()
        $formula = fieldtype_formula::perpare_choices_get_value_function(
            $entities_id,
            $formula,
            'func' . $table_prefix
        );

        if (strstr($formula, '{')) {
            $table_prefix = $table_prefix - 1;
            $formula = functions::prepare_formula_query($entities_id, $formula, $table_prefix);
        }

        return $formula;
    }

    static public function prepare_parent_entities_query($parent_entities, $entities_id, $sql, $table_prefix, $e_prefix)
    {
        $e_prefix = ($table_prefix == 100 ? $e_prefix : 'func' . ($table_prefix + 1));

        if (($parent_lavel = array_search($entities_id, $parent_entities)) > 0) {
            $sql .= " and func{$table_prefix}.parent_item_id in ";
            foreach ($parent_entities as $key => $pid) {
                if ($pid != $entities_id) {
                    $sql .= "(select func_" . $pid . ".id from app_entity_" . $pid . " func_" . $pid . " where func_" . $pid . ".parent_item_id in ";
                } else {
                    $sql .= "({$e_prefix}.id)" . str_repeat(')', $parent_lavel);
                }
            }
        } else {
            $sql .= " and func{$table_prefix}.parent_item_id={$e_prefix}.id ";
        }

        return $sql;
    }

}
