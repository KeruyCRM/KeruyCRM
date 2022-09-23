<?php

if (!defined('KERUY_CRM')) {
    exit;
}

//start build item sql data
$sql_data = [];

$choices_values = [];

$email_username = '';
$import_username = '';

$is_unique_item = true;

$forceCommit = \K::model()->forceCommit();

for ($col = 1; $col <= count(\K::$fw->worksheet[\K::$fw->row]); ++$col) {
    if (isset(\K::$fw->import_fields[$col]) and strlen(\K::$fw->worksheet[\K::$fw->row][$col]) > 0) {
        $field_id = \K::$fw->import_fields[$col];

        //skip field import if field ID not the uses Entity
        if (!isset(\K::$fw->app_fields_cache[\K::$fw->entities_id][$field_id])) {
            continue;
        }

        //$filed_info_query = db_query("select * from app_fields where id='" . db_input($field_id) . "'");

        $filed_info = \K::model()->db_fetch_one('app_fields', [
            'id = ?',
            $field_id
        ]);

        if ($filed_info) {
            $cfg = new \Models\Main\Fields_types_cfg($filed_info['configuration']);

            switch ($filed_info['type']) {
                case 'fieldtype_input_ip':
                    $value = ip2long(trim(\K::$fw->worksheet[\K::$fw->row][$col]));
                    $sql_data['field_' . (int)$field_id] = $value;
                    break;
                case 'fieldtype_user_email':
                    $value = trim(\K::$fw->worksheet[\K::$fw->row][$col]);
                    $email_username = substr($value, 0, strpos($value, '@'));

                    $sql_data['field_' . (int)$field_id] = $value;
                    break;
                case 'fieldtype_user_username':
                    $value = trim(\K::$fw->worksheet[\K::$fw->row][$col]);
                    $import_username = $value;

                    $sql_data['field_' . (int)$field_id] = $value;
                    break;
                case 'fieldtype_entity':
                case 'fieldtype_entity_ajax':
                case 'fieldtype_entity_multilevel':
                    $values_list = [];
                    $value = trim(\K::$fw->worksheet[\K::$fw->row][$col]);

                    if ($heading_id = \Models\Main\Fields::get_heading_id($cfg->get('entity_id'))) {
                        $heading_field_info = \K::model()->db_find('app_fields', $heading_id);
                        if (in_array(
                            $heading_field_info['type'],
                            [
                                'fieldtype_input',
                                'fieldtype_input_masked',
                                'fieldtype_text_pattern_static',
                                'fieldtype_input_url'
                            ]
                        )) {
                            $value_array = [];
                            if ($filed_info['type'] == 'fieldtype_entity_multilevel' or $cfg->get(
                                    'display_as'
                                ) == 'dropdown') {
                                $value_array[] = $value;
                            } else {
                                $value_array = explode(',', $value);
                            }

                            foreach ($value_array as $value_name) {
                                /*$item_query = db_query(
                                    "select id from app_entity_" . $cfg->get(
                                        'entity_id'
                                    ) . " where field_" . $heading_id . "='" . db_input($value_name) . "'"
                                );*/

                                $item = \K::model()->db_fetch_one('app_entity_' . (int)$cfg->get('entity_id'), [
                                    'field_' . (int)$heading_id . ' = ?',
                                    $value_name
                                ], [], 'id');

                                if ($item) {
                                    $values_list[] = $item['id'];
                                } else {
                                    $parent_entities_item_id = 0;

                                    if (($parent_entities_id = \K::$fw->app_entities_cache[$cfg->get(
                                            'entity_id'
                                        )]['parent_id']) > 0) {
                                        //$check_query = db_query("select id from app_entity_" . $cfg->get('entity_id'));

                                        $check = \K::model()->db_fetch_one(
                                            'app_entity_' . (int)$cfg->get('entity_id'),
                                            [],
                                            [],
                                            'id'
                                        );

                                        if ($check) {
                                            $parent_entities_item_id = $check['id'];
                                        }
                                    }

                                    $item_sql_data = [];
                                    $item_sql_data['field_' . (int)$heading_id] = trim($value_name);
                                    $item_sql_data['date_added'] = time();
                                    $item_sql_data['created_by'] = \K::$fw->app_logged_users_id;
                                    $item_sql_data['parent_item_id'] = $parent_entities_item_id;

                                    $mapper = \K::model()->db_perform(
                                        'app_entity_' . (int)$cfg->get('entity_id'),
                                        $item_sql_data
                                    );

                                    $item_id = \K::model()->db_insert_id($mapper);

                                    $values_list[] = $item_id;
                                }
                            }

                            //prepare choices values
                            $choices_values[$field_id] = $values_list;

                            $sql_data['field_' . (int)$field_id] = implode(',', $values_list);
                        }
                    }
                    break;
                case 'fieldtype_dropdown':
                case 'fieldtype_radioboxes':
                case 'fieldtype_stages':
                    $value = trim(\K::$fw->worksheet[\K::$fw->row][$col]);

                    if ($cfg->get('use_global_list') > 0) {
                        if (isset(\K::$fw->global_choices_names_to_id[$cfg->get('use_global_list')][$value])) {
                            $sql_data['field_' . (int)$field_id] = \K::$fw->global_choices_names_to_id[$cfg->get(
                                'use_global_list'
                            )][$value];
                        } else {
                            /*$fields_choices_info_query = db_query(
                                "select * from app_global_lists_choices where name='" . db_input(
                                    $value
                                ) . "' and lists_id='" . db_input($cfg->get('use_global_list')) . "'"
                            );*/

                            $fields_choices_info = \K::model()->db_fetch_one('app_global_lists_choices', [
                                'name = ? and lists_id = ?',
                                $value,
                                $cfg->get('use_global_list')
                            ]);

                            if ($fields_choices_info) {
                                $sql_data['field_' . (int)$field_id] = $fields_choices_info['id'];

                                \K::$fw->global_choices_names_to_id[$cfg->get(
                                    'use_global_list'
                                )][$value] = $fields_choices_info['id'];
                            } else {
                                $field_sql_data = [
                                    'lists_id' => $cfg->get('use_global_list'),
                                    'parent_id' => 0,
                                    'name' => $value
                                ];

                                $mapper = \K::model()->db_perform('app_global_lists_choices', $field_sql_data);

                                $item_id = \K::model()->db_insert_id($mapper);

                                $sql_data['field_' . (int)$field_id] = $item_id;

                                \K::$fw->global_choices_names_to_id[$cfg->get('use_global_list')][$value] = $item_id;
                            }
                        }
                    } elseif (isset(\K::$fw->choices_names_to_id[$field_id][$value])) {
                        $sql_data['field_' . (int)$field_id] = \K::$fw->choices_names_to_id[$field_id][$value];
                    } else {
                        /*$fields_choices_info_query = db_query(
                            "select * from app_fields_choices where name='" . db_input(
                                $value
                            ) . "' and fields_id='" . db_input($field_id) . "'"
                        );*/

                        $fields_choices_info = \K::model()->db_fetch_one('app_fields_choices', [
                            'name = ? and fields_id = ?',
                            $value,
                            $field_id
                        ]);

                        if ($fields_choices_info) {
                            $sql_data['field_' . (int)$field_id] = $fields_choices_info['id'];

                            \K::$fw->choices_names_to_id[$field_id][$value] = $fields_choices_info['id'];
                        } else {
                            $field_sql_data = [
                                'fields_id' => $field_id,
                                'parent_id' => 0,
                                'name' => $value
                            ];

                            $mapper = \K::model()->db_perform('app_fields_choices', $field_sql_data);

                            $item_id = \K::model()->db_insert_id($mapper);

                            $sql_data['field_' . (int)$field_id] = $item_id;

                            \K::$fw->choices_names_to_id[$field_id][$value] = $item_id;
                        }
                    }

                    //prepare choices values
                    $choices_values[$field_id][] = $sql_data['field_' . (int)$field_id];

                    break;
                case 'fieldtype_dropdown_multilevel':
                    $values_list = [];
                    $value = trim(\K::$fw->worksheet[\K::$fw->row][$col]);

                    if (strlen($value)) {
                        $value_id = 0;

                        if ($cfg->get('use_global_list') > 0) {
                            if (isset(\K::$fw->global_choices_names_to_id[$cfg->get('use_global_list')][$value])) {
                                $value_id = \K::$fw->global_choices_names_to_id[$cfg->get('use_global_list')][$value];
                            } else {
                                /*$fields_choices_info_query = db_query(
                                    "select * from app_global_lists_choices where name='" . db_input(
                                        trim($value)
                                    ) . "' and lists_id='" . db_input($cfg->get('use_global_list')) . "'"
                                );*/

                                $fields_choices_info = \K::model()->db_fetch_one('app_global_lists_choices', [
                                    'name = ? and lists_id = ?',
                                    $value,
                                    $cfg->get('use_global_list')
                                ]);

                                if ($fields_choices_info) {
                                    $value_id = $fields_choices_info['id'];
                                    \K::$fw->global_choices_names_to_id[$cfg->get(
                                        'use_global_list'
                                    )][$value] = $value_id;
                                }
                            }
                        } elseif (isset(\K::$fw->choices_names_to_id[$field_id][$value])) {
                            $value_id = \K::$fw->choices_names_to_id[$field_id][$value];
                        } else {
                            /*$fields_choices_info_query = db_query(
                                "select * from app_fields_choices where name='" . db_input(
                                    trim($value)
                                ) . "' and fields_id='" . db_input($field_id) . "'"
                            );*/

                            $fields_choices_info = \K::model()->db_fetch_one('app_fields_choices', [
                                'name = ? and fields_id = ?',
                                $value,
                                $field_id
                            ]);

                            if ($fields_choices_info) {
                                $value_id = $fields_choices_info['id'];
                                \K::$fw->choices_names_to_id[$field_id][$value] = $value_id;
                            }
                        }

                        if ($value_id > 0) {
                            if ($cfg->get('use_global_list')) {
                                if (isset(\K::$fw->global_choices_parents_to_idd[$value_id])) {
                                    $value_array = \K::$fw->global_choices_parents_to_idd[$value_id];
                                } else {
                                    $value_array = \Models\Main\Global_lists::get_parent_ids($value_id);

                                    \K::$fw->global_choices_parents_to_idd[$value_id] = $value_array;
                                }
                            } elseif (isset(\K::$fw->choices_parents_to_id[$field_id][$value_id])) {
                                $value_array = \K::$fw->choices_parents_to_id[$field_id][$value_id];
                            } else {
                                $value_array = \Models\Main\Fields_choices::get_parent_ids($value_id);

                                \K::$fw->choices_parents_to_id[$field_id][$value_id] = $value_array;
                            }

                            $values_list = array_reverse($value_array);

                            //prepare choices values
                            $choices_values[$field_id] = $values_list;

                            $sql_data['field_' . (int)$field_id] = implode(',', $values_list);
                        }
                    }

                    break;
                case 'fieldtype_grouped_users':
                case 'fieldtype_dropdown_multiple':
                case 'fieldtype_checkboxes':
                case 'fieldtype_tags':
                    $values_list = [];
                    $value = trim(\K::$fw->worksheet[\K::$fw->row][$col]);

                    if ($cfg->get('use_global_list') > 0) {
                        $exp = explode(',', $value);

                        foreach ($exp as $value_name) {
                            /*$fields_choices_info_query = db_query(
                                "select * from app_global_lists_choices where name='" . db_input(
                                    trim($value_name)
                                ) . "' and lists_id='" . db_input($cfg->get('use_global_list')) . "'"
                            );*/

                            $fields_choices_info = \K::model()->db_fetch_one('app_global_lists_choices', [
                                'name = ? and lists_id = ?',
                                trim($value_name),
                                $cfg->get('use_global_list')
                            ]);

                            if ($fields_choices_info) {
                                $values_list[] = $fields_choices_info['id'];
                            } else {
                                $field_sql_data = [
                                    'lists_id' => $cfg->get('use_global_list'),
                                    'parent_id' => 0,
                                    'name' => trim($value_name)
                                ];

                                $mapper = \K::model()->db_perform('app_global_lists_choices', $field_sql_data);

                                $item_id = \K::model()->db_insert_id($mapper);

                                $values_list[] = $item_id;
                            }
                        }
                    } else {
                        $exp = explode(',', $value);

                        foreach ($exp as $value_name) {
                            /*$fields_choices_info_query = db_query(
                                "select * from app_fields_choices where name='" . db_input(
                                    trim($value_name)
                                ) . "' and fields_id='" . db_input($field_id) . "'"
                            );*/

                            $fields_choices_info = \K::model()->db_fetch_one('app_fields_choices', [
                                'name = ? and fields_id = ?',
                                trim($value_name),
                                $field_id
                            ]);

                            if ($fields_choices_info) {
                                $values_list[] = $fields_choices_info['id'];
                            } else {
                                $field_sql_data = [
                                    'fields_id' => $field_id,
                                    'parent_id' => 0,
                                    'name' => trim($value_name)
                                ];

                                $mapper = \K::model()->db_perform('app_fields_choices', $field_sql_data);

                                $item_id = \K::model()->db_insert_id($mapper);

                                $values_list[] = $item_id;
                            }
                        }
                    }

                    //prepare choices values
                    $choices_values[$field_id] = $values_list;

                    $sql_data['field_' . (int)$field_id] = implode(',', $values_list);

                    break;
                case 'fieldtype_input_date':
                case 'fieldtype_input_datetime':
                    $sql_data['field_' . (int)$field_id] = strtotime(\K::$fw->worksheet[\K::$fw->row][$col]);
                    break;
                default:
                    $sql_data['field_' . (int)$field_id] = \K::$fw->worksheet[\K::$fw->row][$col];
                    break;
            }

            //check uniques
            if (in_array($filed_info['id'], \K::$fw->unique_fields)) {
                /*$check_query = db_query(
                    "select id from app_entity_{\K::$fw->entities_id} where field_{$field_id}='" . db_input(
                        $sql_data['field_' . (int)$field_id]
                    ) . "' limit 1"
                );*/

                $check = \K::model()->db_fetch_one('app_entity_' . (int)\K::$fw->entities_id, [
                    'field_' . (int)$field_id . ' = ?',
                    $sql_data['field_' . (int)$field_id]
                ], [], 'id');

                if ($check) {
                    $is_unique_item = false;
                }
            }
        }
    }
}

//if import users then set required fields for users entity
if (\K::$fw->entities_id == 1 and \K::$fw->POST['import_action'] == 'import') {
    $sql_data['field_6'] = \K::$fw->POST['users_group_id'];
    $sql_data['field_5'] = 1;
    $sql_data['field_13'] = \K::$fw->CFG_APP_LANGUAGE;
    $sql_data['field_14'] = 'default';

    if (strlen($import_username) == 0) {
        $sql_data['field_12'] = $email_username;
    }

    if (isset(\K::$fw->POST['set_pwd_as_username'])) {
        $password = (strlen($import_username) > 0 ? $import_username : $email_username);
    } else {
        $password = \Models\Main\Users\Users::get_random_password();
    }

    $sql_data['password'] = \K::security()->password_hash($password);

    /*$check_query = db_query(
        "select count(*) as total from app_entity_1 where field_12='" . db_input($sql_data['field_12']) . "'"
    );
    $check = db_fetch_array($check_query);*/

    $check = \K::model()->db_fetch_count('app_entity_1', [
        'field_12 = ?',
        $sql_data['field_12']
    ]);

    if ($check > 0) {
        \K::$fw->already_exist_username[] = $sql_data['field_12'];

        $is_unique_item = false;
    }
} elseif (\K::$fw->entities_id == 1 and isset(\K::$fw->POST['set_pwd_as_username'])) {
    $password = (strlen($import_username) > 0 ? $import_username : $email_username);
    $sql_data['password'] = \K::security()->password_hash($password);
}

//prepare multilevel import
if (\K::$fw->multilevel_import > 0) {
    if (\K::$fw->POST['import_action'] == 'import') {
        \K::$fw->POST['import_action'] = 'update_import';
    }

    $heading_field_id = \Models\Main\Fields::get_heading_id(\K::$fw->entities_id);
    \K::$fw->POST['update_by_field'] = $heading_field_id;

    foreach (\K::$fw->import_fields as $c => $v) {
        if ($v == $heading_field_id) {
            if (in_array(
                    \K::$fw->app_fields_cache[\K::$fw->entities_id][$heading_field_id]['type'],
                    ['fieldtype_entity', 'fieldtype_entity_ajax', 'fieldtype_entity_multilevel']
                ) and isset($sql_data['field_' . $heading_field_id])) {
                \K::$fw->POST['update_use_column'] = 'data:' . $sql_data['field_' . $heading_field_id];
            } else {
                \K::$fw->POST['update_use_column'] = $c;
            }
        }
    }
}

//do update
$item_id = false;
$item_has_updated = false;
if (\K::$fw->POST['import_action'] == 'update' or \K::$fw->POST['import_action'] == 'update_import') {
    $field_info = \K::model()->db_find('app_fields', \K::$fw->POST['update_by_field']);

    $use_column_value = (substr(\K::$fw->POST['update_use_column'], 0, 5) == 'data:' ? substr(
        \K::$fw->POST['update_use_column'],
        5
    ) : \K::$fw->worksheet[\K::$fw->row][\K::$fw->POST['update_use_column']]);

    $where_sql = '';
    $where_value = [];
    if ($field_info['type'] == 'fieldtype_id') {
        // $where_sql = " where id='" . db_input($use_column_value) . "'";
        $where_sql = 'id = :id';
        $where_value[':id'] = $use_column_value;
    } else {
        //$where_sql = " where field_" . $field_info['id'] . "='" . db_input($use_column_value) . "'";
        $where_sql = 'field_' . (int)$field_info['id'] . ' = :field';
        $where_value[':field'] = $use_column_value;
    }

    //$where_sql .= " and parent_item_id = '" . \K::$fw->import_entity_parent_item_id . "'";
    $where_sql .= ' and parent_item_id = :parent_item_id';
    $where_value[':parent_item_id'] = \K::$fw->import_entity_parent_item_id;

    //$item_query = db_query("select id from app_entity_" . \K::$fw->entities_id . $where_sql);

    $item = \K::model()->db_fetch_one(
        'app_entity_' . (int)\K::$fw->entities_id,
        [
            $where_sql
        ] + $where_value,
        [],
        'id'
    );

    if ($item and count($sql_data)) {
        \K::model()->db_update('app_entity_' . (int)\K::$fw->entities_id, $sql_data, [
            'id = ?',
            $item['id']
        ]);
        $item_has_updated = true;

        \K::$fw->count_items_updated++;

        $item_id = $item['id'];

        \K::$fw->import_entity_parent_item_id = $item_id;
    }
}

//do insert
if (!$item_has_updated and (\K::$fw->POST['import_action'] == 'import' or \K::$fw->POST['import_action'] == 'update_import')) {
    //skip not unique items
    if ($is_unique_item) {
        //set other values
        $sql_data['date_added'] = time();
        $sql_data['created_by'] = \K::$fw->app_logged_users_id;
        $sql_data['parent_item_id'] = (int)\K::$fw->import_entity_parent_item_id;

        $mapper = \K::model()->db_perform('app_entity_' . (int)\K::$fw->entities_id, $sql_data);

        $item_id = \K::model()->db_insert_id($mapper);

        \K::$fw->import_entity_parent_item_id = $item_id;

        \K::$fw->count_items_added++;

        if (\Helpers\App::is_ext_installed()) {
            //run actions after item insert
            $processes = new processes(\K::$fw->entities_id);
            $processes->run_after_insert($item_id);
        }
    }
}

//insert choices values if exist
if (count($choices_values) > 0 and $item_id) {
    //reset current choices values if action is "update"
    if (\K::$fw->POST['import_action'] != 'import') {
        /*db_query(
            "delete from app_entity_" . \K::$fw->entities_id . "_values where items_id = '" . $item_id . "' and fields_id='" . $field_id . "'"
        );*/

        //TODO Only last value delete OR foreach?
        \K::model()->db_delete('app_entity_' . (int)\K::$fw->entities_id . '_values', [
            'items_id = ? and fields_id = ?',
            $item_id,
            $field_id
        ]);
    }

    foreach ($choices_values as $field_id => $values) {
        foreach ($values as $value) {
            /*db_query(
                 "INSERT INTO app_entity_" . \K::$fw->entities_id . "_values (items_id, fields_id, value) VALUES ('" . $item_id . "', '" . $field_id . "', '" . $value . "');"
             );*/

            \K::model()->db_perform('app_entity_' . (int)\K::$fw->entities_id . '_values', [
                'items_id' => $item_id,
                'fields_id' => $field_id,
                'value' => $value,
            ]);
        }
    }
}

if ($item_id) {
    //autoupdate all field types
    \Models\Main\Fields_types::update_items_fields(\K::$fw->entities_id, $item_id);
}

if ($forceCommit) {
    \K::model()->commit();
}