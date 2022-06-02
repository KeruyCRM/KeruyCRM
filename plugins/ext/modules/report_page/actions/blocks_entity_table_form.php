<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_report_page_blocks', _GET('id'));
} else {
    $obj = db_show_columns('app_ext_report_page_blocks');

    $max_sort_order_query = db_query(
        "select max(sort_order) as sort_order from app_ext_report_page_blocks where block_type='body_cell' and report_id=" . $report_page['id'] . " and parent_id=" . $block_info['id']
    );
    $max_sort_order = db_fetch_array($max_sort_order_query);

    $obj['sort_order'] = $max_sort_order['sort_order'] + 1;
}