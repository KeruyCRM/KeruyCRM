<?php

$report_page_query = db_query("select * from app_ext_report_page where id='" . _GET('report_id') . "'");
if (!$report_page = db_fetch_array($report_page_query)) {
    redirect_to('ext/report_page/reports');
}

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_report_page_blocks', _GET('id'));
} else {
    $obj = db_show_columns('app_ext_report_page_blocks');

    $max_sort_order_query = db_query(
        "select max(sort_order) as sort_order from app_ext_report_page_blocks where report_id=" . _GET('report_id')
    );
    $max_sort_order = db_fetch_array($max_sort_order_query);

    $obj['sort_order'] = $max_sort_order['sort_order'] + 1;
}

