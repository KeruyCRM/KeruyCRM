<?php

namespace Tools\Items;

class Items_copy
{
    private $entities_id;
    private $items_id;
    private $parent_item_id;
    private $copy_comments, $copy_related_items, $copy_sub_entities;
    private $new_item_id;
    private $sql_data;

    public function __construct($entities_id, $items_id, $settings = [])
    {
        $this->entities_id = $entities_id;
        $this->items_id = $items_id;

        $this->copy_comments = (isset($settings['copy_comments']) ? $settings['copy_comments'] : 0);
        $this->copy_related_items = (isset($settings['copy_related_items']) ? $settings['copy_related_items'] : []);
        $this->copy_sub_entities = (isset($settings['copy_sub_entities']) ? $settings['copy_sub_entities'] : []);
        $this->copy_nested_items = (isset($settings['copy_nested_items']) ? $settings['copy_nested_items'] : 0);

        $this->parent_item_id = false;
        $this->new_item_id = false;
        $this->sql_data = [];
    }

    public function set_sql_data($sql_data)
    {
        $this->sql_data = $sql_data;
    }

    public function set_parent_item_id($id)
    {
        $this->parent_item_id = $id;
    }

    public function run()
    {
        if ($new_item_id = $this->copy_item()) {
            $this->copy_related_items();
            $this->copy_comments();
            $this->copy_sub_entities($this->copy_sub_entities);

            if ($this->copy_nested_items) {
                $this->copy_nested_items($new_item_id);
            }
        }

        return $this->new_item_id;
    }

    public function copy_nested_items($new_item_id)
    {
        $nested_list = tree_table::get_nested_list($this->entities_id, $this->items_id);

        //print_rr($nested_list);

        if (!count($nested_list)) {
            return false;
        }

        //to store new item ID
        $id_to_relace = [];
        $id_to_relace[$this->items_id] = $new_item_id;

        $top_new_item_id = $new_item_id;

        /*
         * Step 1: clone items form nested list and genereate new ID to replace
         */
        foreach ($nested_list as $item) {
            $this->items_id = $item['id'];

            if ($new_item_id = $this->copy_item()) {
                $id_to_relace[$item['id']] = $new_item_id;

                //copy related/comments/subentities for each item tree
                $this->copy_related_items();
                $this->copy_comments();
                $this->copy_sub_entities($this->copy_sub_entities);
            }
        }

        //print_rr($nested_list);
        //print_rr($id_to_relace);

        /*
         * Step 2: prepare parent_id for new created items
         */

        foreach ($nested_list as $item) {
            if ($item['parent_id'] == 0) {
                continue;
            }

            $item_id = $id_to_relace[$item['id']];
            $parent_id = $id_to_relace[$item['parent_id']];

            db_query("update app_entity_{$this->entities_id} set parent_id={$parent_id} where id={$item_id}");
        }

        /*
         * Step 3: update calcaulation 
         */
        $update_item_id = current($id_to_relace);

        //tree table recalculated count/sum
        fieldtype_nested_calculations::update_items_fields($this->entities_id, $update_item_id, 0);

        //keep first new item id to handle redirect after copy
        $this->new_item_id = $top_new_item_id;
    }

    public function copy_item($item_info = null)
    {
        global $app_fields_cache, $app_logged_users_id, $parent_entity_item_id;

        if ($item_info === null) {
            $item_info_query = db_query(
                "select * from app_entity_" . $this->entities_id . " where id='" . db_input($this->items_id) . "'"
            );
            $item_info = db_fetch_array($item_info_query);
        }

        if ($item_info) {
            $parent_entity_item_id = ($this->parent_item_id ? $this->parent_item_id : $item_info['parent_item_id']);

            unset($item_info['id']);

            $sql_data_new = [];
            foreach ($item_info as $k => $v) {
                $sql_data_new[$k] = (isset($this->sql_data[$k]) ? $this->sql_data[$k] : $v);

                if (isset($app_fields_cache[$this->entities_id][substr($k, 6)])) {
                    //attachments
                    if (in_array(
                        $app_fields_cache[$this->entities_id][substr($k, 6)]['type'],
                        fields_types::get_attachments_types()
                    )) {
                        $sql_data_new[$k] = attachments::copy($v);
                    }

                    //barcode
                    if ($app_fields_cache[$this->entities_id][substr($k, 6)]['type'] == 'fieldtype_barcode') {
                        $cfg = new fields_types_cfg(
                            $app_fields_cache[$this->entities_id][substr($k, 6)]['configuration']
                        );
                        if (strlen($cfg->get('template'))) {
                            $sql_data_new[$k] = '';
                        }
                    }

                    //autoincrement
                    if ($app_fields_cache[$this->entities_id][substr($k, 6)]['type'] == 'fieldtype_auto_increment') {
                        $auto_increment = new fieldtype_auto_increment;
                        $sql_data_new[$k] = $auto_increment->increment(
                            $app_fields_cache[$this->entities_id][substr($k, 6)]
                        );
                    }

                    //random value
                    if ($app_fields_cache[$this->entities_id][substr($k, 6)]['type'] == 'fieldtype_random_value') {
                        $random_value = new fieldtype_random_value;
                        $sql_data_new[$k] = $random_value->process([
                            'is_new_item' => true,
                            'value' => '',
                            'field' => $app_fields_cache[$this->entities_id][substr($k, 6)],
                        ]);
                    }
                }
            }

            $sql_data_new['date_added'] = time();
            $sql_data_new['date_updated'] = 0;
            $sql_data_new['created_by'] = $app_logged_users_id;
            $sql_data_new['parent_item_id'] = ($this->parent_item_id ? $this->parent_item_id : $item_info['parent_item_id']);

            db_perform('app_entity_' . $this->entities_id, $sql_data_new);
            $this->new_item_id = $new_item_id = db_insert_id();

            //copy choices values
            $sql_data = [];
            $choices_values_query = db_query(
                "select * from app_entity_" . $this->entities_id . "_values where items_id = " . db_input(
                    $this->items_id
                )
            );
            while ($choices_values = db_fetch_array($choices_values_query)) {
                $sql_data[] = [
                    'items_id' => $this->new_item_id,
                    'fields_id' => $choices_values['fields_id'],
                    'value' => $choices_values['value'],
                ];
            }

            db_batch_insert("app_entity_" . $this->entities_id . "_values", $sql_data);

            //barcode
            $item_query = db_query("select * from app_entity_{$this->entities_id} where id = {$this->new_item_id}");
            $item = db_fetch_array($item_query);
            fieldtype_barcode::update_items_fields($this->entities_id, $this->new_item_id, $item);

            return $new_item_id;
        }

        return false;
    }

    public function copy_related_items()
    {
        if (!count($this->copy_related_items)) {
            return false;
        }

        //copy related records
        $fields_query = db_query(
            "select f.id from app_fields f, app_forms_tabs t where f.type in ('fieldtype_related_records') and f.entities_id='" . db_input(
                $this->entities_id
            ) . "' and f.id in (" . implode(',', $this->copy_related_items) . ") and f.forms_tabs_id=t.id"
        );
        while ($field = db_fetch_array($fields_query)) {
            $reladed_records = new related_records($this->entities_id, $this->items_id);
            $reladed_records->set_related_field($field['id']);
            $related_items = $reladed_records->get_related_items();

            foreach ($related_items as $related_items_id) {
                $reladed_records->add_related_record($this->entities_id, $this->new_item_id, $related_items_id);
            }
        }
    }

    public function copy_comments()
    {
        if ($this->copy_comments != 1) {
            return false;
        }

        $sql_data = [];
        $comments_query = db_query(
            "select * from app_comments where entities_id='" . db_input(
                $this->entities_id
            ) . "' and items_id='" . db_input($this->items_id) . "' order by id"
        );
        while ($comments = db_fetch_array($comments_query)) {
            $sql_data[] = [
                'description' => $comments['description'],
                'entities_id' => $comments['entities_id'],
                'items_id' => $this->new_item_id,
                'attachments' => attachments::copy($comments['attachments']),
                'created_by' => $comments['created_by'],
                'date_added' => $comments['date_added'],
            ];
        }

        if (count($sql_data)) {
            db_batch_insert('app_comments', $sql_data);
        }
    }

    public function copy_sub_entities(
        $copy_sub_entities,
        $entities_id = false,
        $parent_item_id = false,
        $new_item_id = false
    ) {
        //check if exist sub entities
        if (!count($copy_sub_entities)) {
            return false;
        }

        //use default values if not pas
        if (!$parent_item_id) {
            $parent_item_id = $this->items_id;
        }
        if (!$new_item_id) {
            $new_item_id = $this->new_item_id;
        }
        if (!$entities_id) {
            $entities_id = $this->entities_id;
        }

        //get entities 
        $entities_query = db_query(
            "select * from app_entities where id in (" . implode(
                ',',
                $copy_sub_entities
            ) . ") and parent_id='" . $entities_id . "'"
        );
        while ($entities = db_fetch_array($entities_query)) {
            //check sub entities
            $sub_entiteis_list = [];
            $sub_entiteis_query = db_query("select id from app_entities where parent_id='" . $entities['id'] . "'");
            while ($sub_entiteis = db_fetch_array($sub_entiteis_query)) {
                $sub_entiteis_list[] = $sub_entiteis['id'];
            }

            //copy items
            $items_query = db_query(
                "select * from app_entity_" . $entities['id'] . " where parent_item_id='" . $parent_item_id . "'"
            );
            while ($items = db_fetch_array($items_query)) {
                $copy_process = new items_copy($entities['id'], $items['id']);
                $copy_process->set_parent_item_id($new_item_id);
                $copy_item_id = $copy_process->copy_item($items);

                //copy nested
                $copy_process->copy_nested_items($copy_item_id);

                //copy sub items
                if (count($sub_entiteis_list)) {
                    $copy_process->copy_sub_entities($sub_entiteis_list, $entities['id'], $items['id'], $copy_item_id);
                }
            }
        }
    }
}