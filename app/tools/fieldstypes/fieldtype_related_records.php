<?php

namespace Tools\FieldsTypes;

class Fieldtype_related_records
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::f3()->TEXT_FIELDTYPE_RELATED_RECORDS_TITLE];
    }

    public function get_configuration($params = [])
    {
        $entity_info = db_find('app_entities', $params['entities_id']);

        $cfg = [];

        $cfg[\K::f3()->TEXT_SETTINGS][] = [
            'title' => \K::f3()->TEXT_SELECT_ENTITY,
            'name' => 'entity_id',
            'tooltip' => \K::f3()->TEXT_FIELDTYPE_RELATED_RECORDS_SELECT_ENTITY_TOOLTIP . ' ' . $entity_info['name'],
            'type' => 'dropdown',
            'choices' => entities::get_choices(),
            'params' => ['class' => 'form-control input-medium'],
            'onChange' => 'fields_types_ajax_configuration(\'fields_for_search_box\',this.value)'
        ];

        /*
          $cfg[\K::f3()->TEXT_SETTINGS][] = array('title'=>tooltip_icon(TEXT_ROWS_PER_PAGE_IF_NOT_SET) . \K::f3()->TEXT_ROWS_PER_PAGE,
          'name'=>'rows_per_page',
          'type'=>'input',
          'params'=>array('class'=>'form-control input-xsmall'));
         */

        $cfg[\K::f3()->TEXT_SETTINGS][] = [
            'title' => tooltip_icon(
                    \K::f3()->TEXT_DISPLAY_IN_MAIN_COLUMN_INFO
                ) . \K::f3()->TEXT_DISPLAY_IN_MAIN_COLUMN,
            'name' => 'display_in_main_column',
            'type' => 'checkbox'
        ];

        $cfg[\K::f3()->TEXT_SETTINGS][] = [
            'title' => \K::f3()->TEXT_COLLAPSED,
            'name' => 'is_collapsed',
            'type' => 'checkbox'
        ];
        $cfg[\K::f3()->TEXT_SETTINGS][] = [
            'title' => \K::f3()->TEXT_DISPLAY_SEARCH_BAR,
            'name' => 'display_search_bar',
            'type' => 'checkbox'
        ];

        $cfg[\K::f3()->TEXT_SETTINGS][] = [
            'title' => \K::f3()->TEXT_HIDE_FIELD_IF_NO_RECORDS,
            'name' => 'hide_field_without_records',
            'type' => 'checkbox'
        ];

        $cfg[\K::f3()->TEXT_SETTINGS][] = [
            'title' => \K::f3()->TEXT_HIDE_BUTTONS,
            'name' => 'hide_controls',
            'type' => 'dropdown',
            'choices' => [
                'add' => \K::f3()->TEXT_BUTTON_ADD,
                'bind' => \K::f3()->TEXT_BUTTON_BIND,
                'with_selected' => \K::f3()->TEXT_WITH_SELECTED
            ],
            'params' => ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
        ];

        $cfg[\K::f3()->TEXT_SETTINGS][] = [
            'title' => \K::f3()->TEXT_DISPLAY_IN_LISTING,
            'name' => 'display_in_listing',
            'type' => 'dropdown',
            'choices' => ['count' => \K::f3()->TEXT_COUNT_RELATED_ITEMS, 'list' => \K::f3()->TEXT_LIST_RELATED_ITEMS],
            'params' => ['class' => 'form-control input-medium']
        ];

        /*
          $cfg[\K::f3()->TEXT_SETTINGS][] = array(
          'title'=>tooltip_icon(TEXT_ENTER_TEXT_PATTERN_INFO) . \K::f3()->TEXT_HEADING_PATTER_IN_LINSING,
          'tooltip' => \K::f3()->TEXT_HEADING_TEMPLATE_INFO,
          'name'=>'heading_template',
          'type'=>'textarea',
          'params'=>array('class'=>'form-control input-xlare textarea-small'));
         */

        $cfg[\K::f3()->TEXT_SETTINGS][] = ['name' => 'fields_in_listing', 'type' => 'hidden'];
        $cfg[\K::f3()->TEXT_SETTINGS][] = ['name' => 'fields_in_popup', 'type' => 'hidden'];

        $cfg[\K::f3()->TEXT_SETTINGS][] = ['name' => 'create_related_comment', 'type' => 'hidden'];
        $cfg[\K::f3()->TEXT_SETTINGS][] = ['name' => 'create_related_comment_text', 'type' => 'hidden'];
        $cfg[\K::f3()->TEXT_SETTINGS][] = ['name' => 'delete_related_comment', 'type' => 'hidden'];
        $cfg[\K::f3()->TEXT_SETTINGS][] = ['name' => 'delete_related_comment_text', 'type' => 'hidden'];
        $cfg[\K::f3()->TEXT_SETTINGS][] = ['name' => 'create_related_comment_to', 'type' => 'hidden'];
        $cfg[\K::f3()->TEXT_SETTINGS][] = ['name' => 'create_related_comment_to_text', 'type' => 'hidden'];
        $cfg[\K::f3()->TEXT_SETTINGS][] = ['name' => 'delete_related_comment_to', 'type' => 'hidden'];
        $cfg[\K::f3()->TEXT_SETTINGS][] = ['name' => 'delete_related_comment_to_text', 'type' => 'hidden'];

        //TEXT_FIELDS
        $cfg[\K::f3()->TEXT_LINK_RECORD][] = [
            'name' => 'fields_for_search_box',
            'type' => 'ajax',
            'html' => '<script>fields_types_ajax_configuration(\'fields_for_search_box\',$("#fields_configuration_entity_id").val())</script>'
        ];

        return $cfg;
    }

    public function get_ajax_configuration($name, $value)
    {
        $cfg = [];

        switch ($name) {
            case 'fields_for_search_box':
                $entities_id = $value;

                //search by fields
                $choices = [];

                $fields_query = db_query(
                    "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in (" . fields_types::get_types_for_search_list(
                    ) . ") and  f.entities_id='" . $entities_id . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
                );
                while ($fields = db_fetch_array($fields_query)) {
                    $choices[$fields['id']] = fields_types::get_option($fields['type'], 'name', $fields['name']);
                }

                $cfg[] = [
                    'title' => \K::f3()->TEXT_SEARCH_BY_FIELDS,
                    'name' => 'fields_for_search',
                    'type' => 'dropdown',
                    'choices' => $choices,
                    'tooltip_icon' => \K::f3()->TEXT_SEARCH_BY_FIELDS_INFO,
                    'params' => ['class' => 'form-control chosen-select input-xlarge', 'multiple' => 'multiple']
                ];

                //dorpdown template
                $cfg[] = [
                    'title' => \K::f3()->TEXT_HEADING_TEMPLATE . fields::get_available_fields_helper(
                            $entities_id,
                            'fields_configuration_heading_template'
                        ),
                    'name' => 'heading_template',
                    'type' => 'textarea',
                    'tooltip_icon' => \K::f3()->TEXT_HEADING_TEMPLATE_INFO,
                    'tooltip' => \K::f3()->TEXT_ENTER_TEXT_PATTERN_INFO,
                    'params' => ['class' => 'form-control input-xlarge']
                ];

                break;
        }

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        return false;
    }

    public function process($options)
    {
        return false;
    }

    public function output($options)
    {
        global $current_path_array, $current_entity_id, $current_item_id, $current_path, $app_user;

        //output count of related items 
        $cfg = new fields_types_cfg($options['field']['configuration']);

        if ($cfg->get('display_in_listing') == 'list') {
            $related_records = new related_records($options['field']['entities_id'], $options['item']['id']);
            $related_records->set_related_field($options['field']['id']);

            return $related_records->render_list_in_listing($options);
        } else {
            $related_records = new related_records($options['field']['entities_id'], $options['item']['id']);
            $related_records->set_related_field($options['field']['id']);

            return $related_records->count_related_items();
        }
    }

    public function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $sql = [];

        if (strlen($filters['filters_values']) > 0) {
            $field = db_find('app_fields', $filters['fields_id']);

            $cfg = new fields_types_cfg($field['configuration']);

            $table_info = related_records::get_related_items_table_name(
                $options['entities_id'],
                $cfg->get('entity_id')
            );

            //if quick filters panels then use search function
            if ($filters['filters_condition'] == 'include') {
                $where_sql = '';

                if (strlen($table_info['sufix']) > 0) {
                    $where_sql = " or ri.entity_" . $options['entities_id'] . $table_info['sufix'] . "_items_id=e.id ";
                }

                $search_sql = " and ri.entity_" . $cfg->get('entity_id') . "_items_id in 
	      		(select rie.id from app_entity_" . $cfg->get('entity_id') . " rie where " . (fields::get_heading_id(
                        $cfg->get('entity_id')
                    ) ? "rie.field_" . fields::get_heading_id($cfg->get('entity_id')) . " like '%" . db_input(
                            $filters['filters_values']
                        ) . "%'" : "rie.id='" . db_input($filters['filters_values']) . "'") . ")";

                $sql = "(select count(*) as total from " . $table_info['table_name'] . " ri where (ri.entity_" . $options['entities_id'] . "_items_id=e.id {$where_sql}) {$search_sql})";

                //echo $sql;

                $sql_query[] = $sql . ">0";
            } else {
                $filters_values = (strlen($filters['filters_values']) ? explode(
                    ',',
                    $filters['filters_values']
                ) : [0 => '']);
                $filters_condition = $filters_values[0];
                unset($filters_values[0]);

                $where_sql = '';

                if (strlen($table_info['sufix']) > 0) {
                    $where_sql = " or ri.entity_" . $options['entities_id'] . $table_info['sufix'] . "_items_id=e.id ";
                }

                //add filtes by items
                $where_items_sql = '';
                if (count($filters_values) > 0) {
                    $where_items_sql = " and ri.entity_" . $cfg->get('entity_id') . "_items_id in (" . implode(
                            ',',
                            $filters_values
                        ) . ")";
                }

                $sql = "(select count(*) as total from " . $table_info['table_name'] . " ri where (ri.entity_" . $options['entities_id'] . "_items_id=e.id {$where_sql}) {$where_items_sql})";

                $sql_query[] = ($filters_condition == 'include' ? $sql . ">0" : $sql . "=0");
                //print_rr($sql_query);
            }
        }

        return $sql_query;
    }

    public static function prepare_query_select($entities_id, $listing_sql_query_select, $reports_info = [])
    {
        if (!isset($reports_info['fields_in_listing'])) {
            $reports_info['fields_in_listing'] = '';
        }

        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_related_records') and " . (strlen(
                $reports_info['fields_in_listing']
            ) ? "find_in_set(f.id,'" . $reports_info['fields_in_listing'] . "')" : "f.listing_status=1") . " and f.entities_id='" . db_input(
                $entities_id
            ) . "' and f.forms_tabs_id=t.id  order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($field = db_fetch_array($fields_query)) {
            $cfg = new fields_types_cfg($field['configuration']);

            if ($cfg->get('display_in_listing', 'count') != 'count') {
                continue;
            }

            $table_info = related_records::get_related_items_table_name($entities_id, $cfg->get('entity_id'));

            $where_sql = '';

            if (strlen($table_info['sufix']) > 0) {
                $where_sql = " or ri.entity_" . $entities_id . $table_info['sufix'] . "_items_id=e.id ";
            }

            $listing_sql_query_select .= ", (select count(*) as total from " . $table_info['table_name'] . " ri where ri.entity_" . $entities_id . "_items_id=e.id {$where_sql}) as field_" . $field['id'];
        }

        return $listing_sql_query_select;
    }
}