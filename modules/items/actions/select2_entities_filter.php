<?php

if (!IS_AJAX) {
    //exit();
}

switch ($app_module_action) {
    case 'select_items':

        $entity_info = db_find('app_entities', _POST('field_entity_id'));
        $field = $app_fields_cache[_POST('entity_id')][_POST('field_id')];
        $cfg = new settings($field['configuration']);

        $parent_item_id = isset($_POST['parent_item_id']) ? _POST('parent_item_id') : false;

        $search = isset($_POST['search']) ? $_POST['search'] : '';
        $is_tree_view = (isset($_POST['is_tree_view']) and $_POST['is_tree_view'] == 1 and !strlen(
                $search
            )) ? true : false;

        $listing_sql_query = 'e.id>0' . ($is_tree_view ? " and e.parent_id=0" : "");
        $listing_sql_query_order = '';
        $listing_sql_query_join = '';
        $listing_sql_query_having = '';
        $listing_sql_select = '';

        //check parent entity
        $is_parent_entity_same = false;
        if ($app_entities_cache[_POST(
                'entity_id'
            )]['parent_id'] == $app_entities_cache[$entity_info['id']]['parent_id'] and $parent_item_id > 0) {
            $is_parent_entity_same = true;
            $listing_sql_query .= ' and e.parent_item_id=' . $parent_item_id;
        }


        if (isset($_POST['search'])) {
            $items_search = new items_search($entity_info['id']);
            $items_search->set_search_keywords($_POST['search']);

            if (is_array($cfg->get('fields_for_search'))) {
                $search_fields = [];
                foreach ($cfg->get('fields_for_search') as $id) {
                    $search_fields[] = ['id' => $id];
                }
                $items_search->search_fields = $search_fields;
            }

            $listing_sql_query .= $items_search->build_search_sql_query();
        }

        //check view assigned only access
        $listing_sql_query = items::add_access_query($entity_info['id'], $listing_sql_query);

        //include access to parent records
        $listing_sql_query .= items::add_access_query_for_parent_entities($entity_info['id']);

        $listing_sql_query .= filters_panels::exclude_values_not_in_listing_sql(
            _POST('panel_field_id'),
            _POST('reports_id')
        );

        $listing_sql_query .= fieldtype_entity_ajax::mysql_query_where($cfg, $field, $parent_item_id);

        $listing_sql_query_order .= items::add_listing_order_query_by_entity_id($entity_info['id']);

        //prepare formula query
        if (strlen($heading_template = $cfg->get('heading_template'))) {
            if (preg_match_all('/\[(\d+)\]/', $heading_template, $matches)) {
                $listing_sql_select = fieldtype_formula::prepare_query_select(
                    $cfg->get('entity_id'),
                    '',
                    false,
                    ['fields_in_listing' => implode(',', $matches[1])]
                );
            }
        }


        $results = [];
        $listing_sql = "select  e.* " . $listing_sql_select . " from app_entity_" . $entity_info['id'] . " e " . $listing_sql_query_join . " where " . $listing_sql_query . $listing_sql_query_having . $listing_sql_query_order;

        $listing_split = new split_page($listing_sql, '', 'query_num_rows', 30);
        $items_query = db_query($listing_split->sql_query, false);
        while ($item = db_fetch_array($items_query)) {
            $path_info = items::get_path_info($entity_info['id'], $item['id']);

            //print_r($path_info);

            $parent_name = '';
            if (!$is_parent_entity_same and strlen($path_info['parent_name']) > 0) {
                $parent_name = str_replace('<br>', ' / ', $path_info['parent_name']) . ' / ';
            }

            $text = $parent_name . items::get_heading_field($entity_info['id'], $item['id'], $item);

            $html = '';
            if (strlen($heading_template = $cfg->get('heading_template'))) {
                $fieldtype_text_pattern = new fieldtype_text_pattern();
                $html = $fieldtype_text_pattern->output_singe_text($heading_template, $entity_info['id'], $item);
            }

            $results[] = [
                'id' => $item['id'],
                'text' => $text,
                'html' => '<div>' . (strlen($html) ? $html : $text) . '</div>'
            ];

            if ($is_tree_view) {
                $results = app_select2_nested_items_result($entity_info['id'], $item['id'], $results);
            }
        }

        $response = ['results' => $results];

        if ($listing_split->number_of_pages != $_POST['page'] and $listing_split->number_of_pages > 0) {
            $response['pagination']['more'] = 'true';
        }

        echo json_encode($response);

        exit();

        break;
}