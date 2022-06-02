<?php


$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_export_selected_blocks', _GET('id'));
} else {
    $obj = db_show_columns('app_ext_export_selected_blocks');

    $max_sort_order_query = db_query(
        "select max(sort_order) as sort_order from app_ext_export_selected_blocks where templates_id=" . $template_info['id'] . " and parent_id=" . $row_info['id']
    );
    $max_sort_order = db_fetch_array($max_sort_order_query);

    $obj['sort_order'] = $max_sort_order['sort_order'] + 1;
}