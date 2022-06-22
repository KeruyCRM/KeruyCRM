<?php

namespace Tools\Items;

class Items_search
{

    public $access_schema;
    public $search_fields;
    public $entities_id;
    public $parent_entities_id;
    public $search_keywords;
    public $path;
    public $prefix;

    public function __construct($entities_id)
    {
        global $app_user, $app_fields_cache;

        $this->entities_id = $entities_id;
        $this->prefix = 'e';

        $entities_info = db_find('app_entities', $this->entities_id);

        $this->parent_entities_id = $entities_info['parent_id'];

        //get entity access schema
        $this->access_schema = users::get_entities_access_schema($this->entities_id, $app_user['group_id']);

        $this->search_fields = [];

        //set search by Name by default
        if ($id = fields::get_heading_id($this->entities_id)) {
            if ($app_fields_cache[$this->entities_id][$id]['type'] == 'fieldtype_text_pattern') {
                $cfg = new settings($app_fields_cache[$this->entities_id][$id]['configuration']);
                $pattern = $cfg->get('pattern');
                if (preg_match_all('/\[(\d+)\]/', $pattern, $output_array)) {
                    foreach ($output_array[1] as $id) {
                        $this->search_fields[] = ['id' => $id];
                    }
                }
            } else {
                $this->search_fields[] = ['id' => $id];
            }
        }

        if ($this->entities_id == 1) {
            $this->search_fields[] = ['id' => 7];
            $this->search_fields[] = ['id' => 8];
            $this->search_fields[] = ['id' => 9];
        }

        $this->path = false;
    }

    public function set_path($path)
    {
        $this->path = $path;
    }

    public function set_search_keywords($keywords)
    {
        $this->search_keywords = $keywords;
    }

    public function build_search_sql_query($search_operator = 'or')
    {
        global $app_fields_cache;

        $listing_sql_query = '';

        if (app_parse_search_string($this->search_keywords, $search_keywords, $search_operator)) {
            //print_r($search_keywords);

            $sql_query = [];

            /**
             *  search in fields
             */
            foreach ($this->search_fields as $field) {
                if (isset($search_keywords) && (sizeof($search_keywords) > 0)) {
                    $where_str = "(";
                    for ($i = 0, $n = sizeof($search_keywords); $i < $n; $i++) {
                        switch ($search_keywords[$i]) {
                            case '(':
                            case ')':
                            case 'and':
                            case 'or':
                                $where_str .= " " . $search_keywords[$i] . " ";
                                break;
                            default:
                                $keyword = $search_keywords[$i];

                                switch ($app_fields_cache[$this->entities_id][$field['id']]['type']) {
                                    case 'fieldtype_entity':
                                    case 'fieldtype_entity_ajax':
                                    case 'fieldtype_entity_multilevel':
                                    case 'fieldtype_users':
                                    case 'fieldtype_users_ajax':
                                        $cfg = new settings(
                                            $app_fields_cache[$this->entities_id][$field['id']]['configuration']
                                        );

                                        $entity_id = strlen($cfg->get('entity_id')) ? $cfg->get('entity_id') : 1;
                                        $prefix = 'es' . $entity_id;

                                        $items_search = new items_search($entity_id);
                                        $items_search->set_search_keywords($keyword);
                                        $items_search->prefix = $prefix;
                                        if (strlen($search_sql = $items_search->build_search_sql_query('and'))) {
                                            $where_str_inc = "select {$prefix}.id from app_entity_" . $entity_id . " as $prefix where $prefix.id>0 " . $search_sql;
                                            $where_str .= "(select count(*) from app_entity_" . $this->entities_id . "_values as cv where cv.items_id=e.id and cv.fields_id='" . db_input(
                                                    $field['id']
                                                ) . "' and cv.value in (" . $where_str_inc . "))>0";
                                        }

                                        break;
                                    case 'fieldtype_id':
                                        $where_str .= $this->prefix . ".id like '%" . db_input($keyword) . "%'";
                                        break;
                                    default:
                                        $where_str .= $this->prefix . ".field_" . $field['id'] . " like '%" . db_input(
                                                $keyword
                                            ) . "%'";
                                        break;
                                }

                                break;
                        }
                    }
                    $where_str .= ")";

                    $sql_query[] = $where_str;
                }
            }

            /**
             *  add search by record ID if vlaue is numeric
             */
            if (count($search_keywords) == 1 and is_numeric($search_keywords[0])) {
                $sql_query[] = $this->prefix . ".id='" . db_input($search_keywords[0]) . "'";
            }

            if (count($sql_query) > 0) {
                //print_r($sql_query);

                $listing_sql_query .= ' and (' . implode(' or ', $sql_query) . ')';
            }
        }

        //check parent item
        if ($this->path and $this->parent_entities_id > 0) {
            $path_array = items::parse_path($this->path);

            if ($this->parent_entities_id == $path_array['parent_entity_id']) {
                $listing_sql_query .= " and e.parent_item_id='" . db_input($path_array['parent_entity_item_id']) . "'";
            }
        }

        return $listing_sql_query;
    }

    public function get_choices()
    {
        $choices = [];

        //add search sql query
        $listing_sql_query = $this->build_search_sql_query();

        //check view assigned only access
        $listing_sql_query = items::add_access_query($this->entities_id, $listing_sql_query);

        //include access to parent records
        $listing_sql_query .= items::add_access_query_for_parent_entities($this->entities_id);

        $listing_sql_query .= items::add_listing_order_query_by_entity_id($this->entities_id);

        $items_sql_query = "select e.* from app_entity_" . $this->entities_id . " e where e.id>0 " . $listing_sql_query;
        $items_query = db_query($items_sql_query);
        while ($items = db_fetch_array($items_query)) {
            //add paretn item name if exist
            $parent_name = '';

            if ($this->path and $this->parent_entities_id > 0) {
                $path_array = items::parse_path($this->path);

                if ($this->parent_entities_id != $path_array['parent_entity_id'] and $items['parent_item_id'] > 0) {
                    $parent_name = items::get_heading_field(
                            $this->parent_entities_id,
                            $items['parent_item_id']
                        ) . ' > ';
                }
            }

            $name = items::get_heading_field($this->entities_id, $items['id']);

            $choices[$items['id']] = $parent_name . $name;
        }

        return $choices;
    }
}