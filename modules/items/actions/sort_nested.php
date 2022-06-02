<?php

if (!users::has_access('create')) {
    redirect_to('dashboard/access_forbidden');
}

//keep current listing page
if (isset($_GET['gotopage'])) {
    $listing_page_keeper[key($_GET['gotopage'])] = current($_GET['gotopage']);
}

switch ($app_module_action) {
    case 'sort':
        $choices_sorted = $_POST['choices_sorted'];

        if (strlen($choices_sorted) > 0) {
            $choices_sorted = json_decode(stripslashes($choices_sorted), true);

            tree_table::sort_tree($current_entity_id, $current_item_id, $choices_sorted);

            //recalculate values after sorting
            $item_query = db_query("select parent_id from app_entity_{$current_entity_id} where id={$current_item_id}");
            $item = db_fetch_array($item_query);

            //tree table recalculated count/sum
            fieldtype_nested_calculations::update_items_fields(
                $current_entity_id,
                $current_item_id,
                $item['parent_id']
            );
        }

        if ($app_redirect_to == 'items_info') {
            redirect_to('items/info', 'path=' . $app_path);
        } elseif (strstr($app_redirect_to, 'report_')) {
            redirect_to('reports/view', 'reports_id=' . str_replace('report_', '', $app_redirect_to));
        } else {
            $path_info = items::get_path_info($current_entity_id, $current_item_id);
            redirect_to('items/items', 'path=' . $path_info['path_to_entity']);
        }

        break;
}
