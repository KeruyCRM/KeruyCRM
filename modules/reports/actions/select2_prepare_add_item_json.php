<?php

//check access
if (!IS_AJAX or !users::has_users_access_to_entity(_POST('parent_entity_id'))) {
    exit();
}

switch ($app_module_action) {
    case 'select_items':

        $entity_id = _POST('entity_id');
        $parent_entity_id = _POST('parent_entity_id');

        $listing_sql_query = '';
        $listing_sql_query_join = '';

        //add filters from defualt report
        $default_reports_query = db_query(
            "select * from app_reports where entities_id='" . db_input(
                $parent_entity_id
            ) . "' and reports_type='default'"
        );
        if ($default_reports = db_fetch_array($default_reports_query)) {
            $listing_sql_query = reports::add_filters_query($default_reports['id'], $listing_sql_query);
        }

        if (isset($_POST['search'])) {
            $items_search = new items_search($parent_entity_id);
            $items_search->set_search_keywords($_POST['search']);

            $listing_sql_query .= $items_search->build_search_sql_query('and');
        }

        //check view assigned only access
        $listing_sql_query = items::add_access_query($parent_entity_id, $listing_sql_query);

        //include access to parent records
        $listing_sql_query .= items::add_access_query_for_parent_entities($parent_entity_id);

        $listing_sql_query .= items::add_listing_order_query_by_entity_id($parent_entity_id);

        //build query
        $listing_sql = "select e.* from app_entity_" . $parent_entity_id . " e " . $listing_sql_query_join . "where e.id>0 " . $listing_sql_query;

        $results = [];

        $listing_split = new split_page($listing_sql, '', 'query_num_rows', 30);
        $items_query = db_query($listing_split->sql_query, false);

        while ($item = db_fetch_array($items_query)) {
            $path_info = items::get_path_info($parent_entity_id, $item['id']);

            //print_r($path_info);

            $parent_name = '';
            if (strlen($path_info['parent_name']) > 0) {
                $parent_name = str_replace('<br>', ' / ', $path_info['parent_name']) . ' / ';
            }


            $text = $parent_name . items::get_heading_field($parent_entity_id, $item['id']);

            $results[] = [
                'id' => $path_info['full_path'] . '/' . $entity_id,
                'text' => $text,
                'html' => '<div>' . $text . '</div>'
            ];
        }

        $response = ['results' => $results];

        if ($listing_split->number_of_pages != $_POST['page'] and $listing_split->number_of_pages > 0) {
            $response['pagination']['more'] = 'true';
        }

        echo json_encode($response);

        exit();

        break;
}
