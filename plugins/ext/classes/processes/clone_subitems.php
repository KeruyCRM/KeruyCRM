<?php

class clone_subitems
{

    static function clone_process(
        $actions_id,
        $parent_id = 0,
        $linked_item_id = 0,
        $parent_item_id = 0,
        $item_type = 'parent_item_id'
    ) {
        global $app_fields_cache, $app_user;

        $insert_id = false;

        $rules_query = db_query(
            "select * from app_ext_processes_clone_subitems where actions_id='" . $actions_id . "' and parent_id='" . $parent_id . "'"
        );
        while ($rules = db_fetch_array($rules_query)) {
            $items_query = db_query(
                "select e.* " . fieldtype_formula::prepare_query_select(
                    $rules['from_entity_id'],
                    ''
                ) . " from app_entity_" . $rules['from_entity_id'] . " e where e." . $item_type . "='" . $linked_item_id . "'"
            );
            while ($items = db_fetch_array($items_query)) {
                $choices_values = new choices_values($rules['to_entity_id']);

                //prepare sql data
                $sql_data = [];
                $sql_data['parent_item_id'] = $parent_item_id;
                foreach (preg_split('/\r\n|\r|\n/', $rules['fields']) as $values) {
                    if (!strstr($values, '=')) {
                        continue;
                    }

                    $values = explode('=', str_replace([' ', '[', ']'], '', $values));
                    $from_field_id = trim($values[0]);
                    $to_field_id = trim($values[1]);

                    //echo $from_field_id . ' = ' . $to_field_id . '<br>';
                    //prepare default fields
                    if (isset($items['field_' . $from_field_id]) and is_numeric($to_field_id)) {
                        if (isset($app_fields_cache[$rules['to_entity_id']][$to_field_id])) {
                            if (in_array(
                                $app_fields_cache[$rules['to_entity_id']][$to_field_id]['type'],
                                fields_types::get_attachments_types()
                            )) {
                                $sql_data['field_' . $to_field_id] = attachments::copy(
                                    $items['field_' . $from_field_id]
                                );
                            } else {
                                $sql_data['field_' . $to_field_id] = $items['field_' . $from_field_id];
                            }

                            //prepare choices
                            $process_options = [
                                'class' => $app_fields_cache[$rules['to_entity_id']][$to_field_id]['type'],
                                'value' => $items['field_' . $from_field_id],
                                'field' => $app_fields_cache[$rules['to_entity_id']][$to_field_id],
                            ];

                            $choices_values->prepare($process_options);
                        }
                    } //value from internal fields id or parent_item_id
                    elseif (isset($items[$from_field_id]) and isset($app_fields_cache[$rules['to_entity_id']][$to_field_id])) {
                        $sql_data['field_' . $to_field_id] = $items[$from_field_id];

                        //prepare choices
                        $process_options = [
                            'class' => $app_fields_cache[$rules['to_entity_id']][$to_field_id]['type'],
                            'value' => $items[$from_field_id],
                            'field' => $app_fields_cache[$rules['to_entity_id']][$to_field_id],
                        ];

                        $choices_values->prepare($process_options);
                    } //prepare single value
                    elseif (isset($app_fields_cache[$rules['to_entity_id']][$to_field_id])) {
                        $sql_data['field_' . $to_field_id] = "{$from_field_id}";
                    } //handle parent_item_id for cloned item
                    elseif ($to_field_id == 'parent_item_id' and isset($items[$from_field_id])) {
                        $sql_data['parent_item_id'] = $items[$from_field_id];
                    } elseif ($to_field_id == 'parent_item_id' and isset($items['field_' . $from_field_id])) {
                        $sql_data['parent_item_id'] = $items['field_' . $from_field_id];
                    } elseif ($to_field_id == 'parent_item_id' and is_numeric($from_field_id)) {
                        $sql_data['parent_item_id'] = $from_field_id;
                    }
                }

                $sql_data['date_added'] = time();
                $sql_data['created_by'] = $app_user['id'];

                //print_rr($sql_data);
                //exit();

                db_perform('app_entity_' . $rules['to_entity_id'], $sql_data);
                $insert_id = db_insert_id();

                //insert choices values for fields with multiple values
                $choices_values->process($insert_id);

                //autoupdate all field types
                fields_types::update_items_fields($rules['to_entity_id'], $insert_id);

                //run actions after item insert
                $processes = new processes($rules['to_entity_id']);
                $processes->run_after_insert($insert_id);

                self::clone_process($actions_id, $rules['id'], $items['id'], $insert_id);
            }
        }

        return $insert_id;
    }

    static function get_rules_tree($actions_id, $parent_id = 0, $choices = [], $level = 0)
    {
        $rules_query = db_query(
            "select * from app_ext_processes_clone_subitems where actions_id='" . $actions_id . "' and parent_id='" . $parent_id . "'"
        );
        while ($rules = db_fetch_array($rules_query)) {
            $choices[] = [
                'id' => $rules['id'],
                'parent_id' => $rules['parent_id'],
                'from_entity_id' => $rules['from_entity_id'],
                'to_entity_id' => $rules['to_entity_id'],
                'level' => $level,
            ];

            $choices = self::get_rules_tree($actions_id, $rules['id'], $choices, $level + 1);
        }

        return $choices;
    }

    static function delete_rule($actions_id, $parent_id)
    {
        $rules = self::get_rules_tree($actions_id, $parent_id);

        $rules[] = ['id' => $parent_id];

        foreach ($rules as $rule) {
            db_delete_row('app_ext_processes_clone_subitems', $rule['id']);
        }
    }

    static function clone_nested_items_process($actions_id, $linked_item_id = 0, $parent_item_id = 0)
    {
        $rules_query = db_query(
            "select * from app_ext_processes_clone_subitems where actions_id='" . $actions_id . "'"
        );
        $rules = db_fetch_array($rules_query);

        $from_entity_id = $rules['from_entity_id'];
        $to_entity_id = $rules['to_entity_id'];

        /*
         * Step 0: get nested items list;
         */

        $nested_list = [['parent_id' => 0, 'id' => $linked_item_id]];
        $nested_list = tree_table::get_nested_list($from_entity_id, $linked_item_id, $nested_list);

        //print_rr($nested_list);

        //to store new item ID
        $id_to_relace = [];

        /*
         * Step 1: clone items form nested list and genereate new ID to replace
         */
        foreach ($nested_list as $item) {
            if ($new_item_id = clone_subitems::clone_process($actions_id, 0, $item['id'], $parent_item_id, 'id')) {
                $id_to_relace[$item['id']] = $new_item_id;
            }
        }

        /*
         * Step 2: prepare parent_id for new created items
         */

        foreach ($nested_list as $item) {
            if ($item['parent_id'] == 0) {
                continue;
            }

            $item_id = $id_to_relace[$item['id']];
            $parent_id = $id_to_relace[$item['parent_id']];

            db_query("update app_entity_{$to_entity_id} set parent_id={$parent_id} where id={$item_id}");
        }


        /*
         * Step 3: update calcaulation 
         */
        $update_item_id = current($id_to_relace);

        //tree table recalculated count/sum
        fieldtype_nested_calculations::update_items_fields($to_entity_id, $update_item_id, 0);
    }

}
