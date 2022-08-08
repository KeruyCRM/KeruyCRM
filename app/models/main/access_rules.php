<?php

namespace Models\Main;

class Access_rules
{
    public $access_schema;
    public $fields_view_only_access;
    public $comments_access_schema;

    public function __construct($entities_id, $item_info)
    {
        $this->access_schema = null;
        $this->fields_view_only_access = '';
        $this->comments_access_schema = null;

        //don't check rules for admin
        if (\K::$fw->app_user['group_id'] == 0) {
            return true;
        }

        if (isset(\K::$fw->app_access_rules_fields_cache[$entities_id])) {
            $access_rules_fields = \K::$fw->app_access_rules_fields_cache[$entities_id];

            if (is_numeric($item_info)) {
                $item_info = \K::model()->db_find('app_entity_' . (int)$entities_id, $item_info);
            }

            if (isset($item_info['field_' . $access_rules_fields['fields_id']])) {
                if (strlen($value = $item_info['field_' . $access_rules_fields['fields_id']])) {
                    /*$access_rules_query = db_query(
                        "select * from app_access_rules where find_in_set(" . \K::$fw->app_user['group_id'] . ", users_groups) and find_in_set(" . $value . ",choices) and  entities_id='" . db_input(
                            $entities_id
                        ) . "' and fields_id='" . db_input($access_rules_fields['fields_id']) . "'"
                    );*/

                    $access_rules = \K::model()->db_fetch_one('app_access_rules', [
                        'find_in_set( ? , users_groups) and find_in_set( ? , choices) and entities_id = ? and fields_id = ',
                        \K::$fw->app_user['group_id'],
                        $value,
                        $entities_id,
                        $access_rules_fields['fields_id']
                    ]);

                    if ($access_rules) {
                        $this->access_schema = $access_rules['access_schema'];
                        $this->fields_view_only_access = $access_rules['fields_view_only_access'];
                        $this->comments_access_schema = $access_rules['comments_access_schema'];
                    }
                }
            }
        }
    }

    //get rules cache
    public static function get_access_rules_fields_cache()
    {
        $cache = [];
        //$access_rules_fields_query = \K::model()->db_query_exec("select * from app_access_rules_fields");

        $access_rules_fields_query = \K::model()->db_fetch_all(
            'app_access_rules_fields',
            null,
            [\K::$fw->TTL_APP, 'app_access_rules_fields']
        );

        //while ($access_rules_fields = db_fetch_array($access_rules_fields_query)) {
        foreach ($access_rules_fields_query as $access_rules_fields) {
            $access_rules_fields = $access_rules_fields->cast();

            $cache[$access_rules_fields['entities_id']] = $access_rules_fields;
        }

        return $cache;
    }

    //get rules access schema
    public function get_access_schema()
    {
        if (!isset($this->access_schema)) {
            return null;
        }

        $access_schema = [];
        foreach (\K::$fw->current_access_schema as $val) {
            if (!in_array($val, ['update', 'delete', 'export', 'copy', 'move'])) {
                $access_schema[] = $val;
            }
        }

        if (strlen($this->access_schema)) {
            $exp = explode(',', $this->access_schema);

            foreach ($exp as $val) {
                $access_schema[] = $val;
            }
        }
        return $access_schema;
    }

    //get fields view only access
    public function get_fields_view_only_access()
    {
        if (strlen($this->fields_view_only_access)) {
            $fields_access_schema = [];

            $exp = explode(',', $this->fields_view_only_access);

            foreach ($exp as $field_id) {
                $fields_access_schema[$field_id] = 'view';
            }

            return $fields_access_schema;
        } else {
            return [];
        }
    }

    //get comments access
    public function get_comments_access_schema()
    {
        if (!isset($this->comments_access_schema) or $this->comments_access_schema == 'false') {
            return null;
        }

        $this->comments_access_schema = ($this->comments_access_schema == 'no' ? '' : $this->comments_access_schema);

        return (strlen($this->comments_access_schema) ? explode(',', $this->comments_access_schema) : []);
    }

    public static function has_add_buttons_access($entities_id, $parent_item_id)
    {
        if (\K::$fw->app_entities_cache[$entities_id]['parent_id'] == 0) {
            return true;
        } else {
            /*$reports_info_query = db_query(
                "select * from app_reports where entities_id='" . db_input(
                    \K::$fw->app_entities_cache[$entities_id]['parent_id']
                ) . "' and reports_type='hide_add_button_rules" . $entities_id . "'"
            );*/

            $reports_info = \K::model()->db_fetch_one('app_reports', [
                'entities_id = ? and reports_type = ?',
                \K::$fw->app_entities_cache[$entities_id]['parent_id'],
                'hide_add_button_rules' . $entities_id
            ], [], 'id,entities_id');

            if ($reports_info) {
                //prepare formulas query
                $listing_sql_query_select = \Tools\FieldsTypes\Fieldtype_formula::prepare_query_select(
                    $reports_info['entities_id'],
                    ''
                );

                $listing_sql_query = \Models\Main\Reports\Reports::add_filters_query($reports_info['id'], '');

                //prepare having query for formula fields
                if (isset(\K::$fw->sql_query_having[$reports_info['entities_id']])) {
                    $listing_sql_query .= \Models\Main\Reports\Reports::prepare_filters_having_query(
                        \K::$fw->sql_query_having[$reports_info['entities_id']]
                    );
                }

                //has access if not filters setup
                if (!strlen($listing_sql_query)) {
                    return true;
                }

                $item_info = \K::model()->db_query_exec_one(
                    "select e.* " . $listing_sql_query_select . " from app_entity_" . (int)$reports_info['entities_id'] . " e where e.id = " . (int)$parent_item_id . " " . $listing_sql_query
                );

                if ($item_info) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
    }
}