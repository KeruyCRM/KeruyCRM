<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_related_records
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_RELATED_RECORDS_TITLE];
    }

    public function get_configuration($params = [])
    {
        $entity_info = \K::model()->db_find('app_entities', $params['entities_id']);

        $cfg = [];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_SELECT_ENTITY,
            'name' => 'entity_id',
            'tooltip' => \K::$fw->TEXT_FIELDTYPE_RELATED_RECORDS_SELECT_ENTITY_TOOLTIP . ' ' . $entity_info['name'],
            'type' => 'dropdown',
            'choices' => \Models\Main\Entities::get_choices(),
            'params' => ['class' => 'form-control input-medium'],
            'onChange' => 'fields_types_ajax_configuration(\'fields_for_search_box\',this.value)'
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_DISPLAY_IN_MAIN_COLUMN_INFO
                ) . \K::$fw->TEXT_DISPLAY_IN_MAIN_COLUMN,
            'name' => 'display_in_main_column',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_COLLAPSED,
            'name' => 'is_collapsed',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_DISPLAY_SEARCH_BAR,
            'name' => 'display_search_bar',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_IF_NO_RECORDS,
            'name' => 'hide_field_without_records',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HIDE_BUTTONS,
            'name' => 'hide_controls',
            'type' => 'dropdown',
            'choices' => [
                'add' => \K::$fw->TEXT_BUTTON_ADD,
                'bind' => \K::$fw->TEXT_BUTTON_BIND,
                'with_selected' => \K::$fw->TEXT_WITH_SELECTED
            ],
            'params' => ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_DISPLAY_IN_LISTING,
            'name' => 'display_in_listing',
            'type' => 'dropdown',
            'choices' => ['count' => \K::$fw->TEXT_COUNT_RELATED_ITEMS, 'list' => \K::$fw->TEXT_LIST_RELATED_ITEMS],
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = ['name' => 'fields_in_listing', 'type' => 'hidden'];
        $cfg[\K::$fw->TEXT_SETTINGS][] = ['name' => 'fields_in_popup', 'type' => 'hidden'];
        $cfg[\K::$fw->TEXT_SETTINGS][] = ['name' => 'create_related_comment', 'type' => 'hidden'];
        $cfg[\K::$fw->TEXT_SETTINGS][] = ['name' => 'create_related_comment_text', 'type' => 'hidden'];
        $cfg[\K::$fw->TEXT_SETTINGS][] = ['name' => 'delete_related_comment', 'type' => 'hidden'];
        $cfg[\K::$fw->TEXT_SETTINGS][] = ['name' => 'delete_related_comment_text', 'type' => 'hidden'];
        $cfg[\K::$fw->TEXT_SETTINGS][] = ['name' => 'create_related_comment_to', 'type' => 'hidden'];
        $cfg[\K::$fw->TEXT_SETTINGS][] = ['name' => 'create_related_comment_to_text', 'type' => 'hidden'];
        $cfg[\K::$fw->TEXT_SETTINGS][] = ['name' => 'delete_related_comment_to', 'type' => 'hidden'];
        $cfg[\K::$fw->TEXT_SETTINGS][] = ['name' => 'delete_related_comment_to_text', 'type' => 'hidden'];

        $cfg[\K::$fw->TEXT_LINK_RECORD][] = [
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

                $fields_query = \K::model()->db_query_exec(
                    "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in (" . \Models\Main\Fields_types::get_types_for_search_list(
                    ) . ") and  f.entities_id = ? and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name",
                    $entities_id,
                    'app_fields,app_forms_tabs'
                );

                //while ($fields = db_fetch_array($fields_query)) {
                foreach ($fields_query as $fields) {
                    $choices[$fields['id']] = \Models\Main\Fields_types::get_option(
                        $fields['type'],
                        'name',
                        $fields['name']
                    );
                }

                $cfg[] = [
                    'title' => \K::$fw->TEXT_SEARCH_BY_FIELDS,
                    'name' => 'fields_for_search',
                    'type' => 'dropdown',
                    'choices' => $choices,
                    'tooltip_icon' => \K::$fw->TEXT_SEARCH_BY_FIELDS_INFO,
                    'params' => ['class' => 'form-control chosen-select input-xlarge', 'multiple' => 'multiple']
                ];

                //dropdown template
                $cfg[] = [
                    'title' => \K::$fw->TEXT_HEADING_TEMPLATE . \Models\Main\Fields::get_available_fields_helper(
                            $entities_id,
                            'fields_configuration_heading_template'
                        ),
                    'name' => 'heading_template',
                    'type' => 'textarea',
                    'tooltip_icon' => \K::$fw->TEXT_HEADING_TEMPLATE_INFO,
                    'tooltip' => \K::$fw->TEXT_ENTER_TEXT_PATTERN_INFO,
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
        //output count of related items
        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        if ($cfg->get('display_in_listing') == 'list') {
            $related_records = new \Tools\Related_records($options['field']['entities_id'], $options['item']['id']);
            $related_records->set_related_field($options['field']['id']);

            return $related_records->render_list_in_listing($options);
        } else {
            $related_records = new \Tools\Related_records($options['field']['entities_id'], $options['item']['id']);
            $related_records->set_related_field($options['field']['id']);

            return $related_records->count_related_items();
        }
    }

    public function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        if (strlen($filters['filters_values']) > 0) {
            $field = \K::model()->db_find('app_fields', $filters['fields_id']);

            $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

            $table_info = \Tools\Related_records::get_related_items_table_name(
                $options['entities_id'],
                $cfg->get('entity_id')
            );

            //if quick filters panels then use search function
            if ($filters['filters_condition'] == 'include') {
                $where_sql = '';

                if (strlen($table_info['suffix']) > 0) {
                    $where_sql = " or ri.entity_" . (int)$options['entities_id'] . $table_info['suffix'] . "_items_id = e.id ";
                }

                $search_sql = " and ri.entity_" . (int)$cfg->get('entity_id') . "_items_id in 
	      		(select rie.id from app_entity_" . (int)$cfg->get(
                        'entity_id'
                    ) . " rie where " . (\Models\Main\Fields::get_heading_id(
                        $cfg->get('entity_id')
                    ) ? "rie.field_" . (int)\Models\Main\Fields::get_heading_id(
                            $cfg->get('entity_id')
                        ) . " like " . \K::model()->quote(
                            '%' . $filters['filters_values'] . '%'
                        ) : "rie.id = " . (int)$filters['filters_values']) . ")";

                $sql = "(select count(*) as total from " . $table_info['table_name'] . " ri where (ri.entity_" . (int)$options['entities_id'] . "_items_id = e.id {$where_sql}) {$search_sql})";

                $sql_query[] = $sql . " > 0";
            } else {
                $filters_values = (strlen($filters['filters_values']) ? explode(
                    ',',
                    $filters['filters_values']
                ) : [0 => '']);
                $filters_condition = $filters_values[0];
                unset($filters_values[0]);

                $where_sql = '';

                if (strlen($table_info['suffix']) > 0) {
                    $where_sql = " or ri.entity_" . (int)$options['entities_id'] . $table_info['suffix'] . "_items_id = e.id ";
                }

                //add filters by items
                $where_items_sql = '';
                if (count($filters_values) > 0) {
                    $where_items_sql = " and ri.entity_" . (int)$cfg->get('entity_id') . "_items_id in (" . \K::model(
                        )->quoteToString($filters_values, \PDO::PARAM_INT) . ")";
                }

                $sql = "(select count(*) as total from " . $table_info['table_name'] . " ri where (ri.entity_" . (int)$options['entities_id'] . "_items_id = e.id {$where_sql}) {$where_items_sql})";

                $sql_query[] = ($filters_condition == 'include' ? $sql . " > 0" : $sql . " = 0");
            }
        }

        return $sql_query;
    }

    public static function prepare_query_select($entities_id, $listing_sql_query_select, $reports_info = [])
    {
        if (!isset($reports_info['fields_in_listing'])) {
            $reports_info['fields_in_listing'] = '';
        }

        $fields_query = \K::model()->db_query_exec(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_related_records') and " . (strlen(
                $reports_info['fields_in_listing']
            ) ? "find_in_set(f.id," . \K::model()->quote(
                    $reports_info['fields_in_listing']
                ) . ")" : "f.listing_status = 1") . " and f.entities_id = ? and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name",
            $entities_id,
            'app_fields,app_forms_tabs'
        );
        //while ($field = db_fetch_array($fields_query)) {
        foreach ($fields_query as $field) {
            $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

            if ($cfg->get('display_in_listing', 'count') != 'count') {
                continue;
            }

            $table_info = \Tools\Related_records::get_related_items_table_name($entities_id, $cfg->get('entity_id'));

            $where_sql = '';

            if (strlen($table_info['suffix']) > 0) {
                $where_sql = " or ri.entity_" . (int)$entities_id . $table_info['suffix'] . "_items_id = e.id ";
            }

            $listing_sql_query_select .= ", (select count(*) as total from " . $table_info['table_name'] . " ri where ri.entity_" . (int)$entities_id . "_items_id = e.id {$where_sql}) as field_" . (int)$field['id'];
        }

        return $listing_sql_query_select;
    }
}