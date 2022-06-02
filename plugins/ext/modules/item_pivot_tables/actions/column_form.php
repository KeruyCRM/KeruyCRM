<?php

$reports_query = db_query("select * from app_ext_item_pivot_tables where id='" . _get::int('reports_id') . "'");
if (!$reports = db_fetch_array($reports_query)) {
    redirect_to('ext/item_pivot_tables/reports');
}

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_item_pivot_tables_calcs', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_item_pivot_tables_calcs');
}
