<?php

$pivot_reports_query = db_query(
    "select * from app_ext_item_pivot_tables where id='" . str_replace('item_pivot_tables_', '', $app_redirect_to) . "'"
);
$pivot_reports = db_fetch_array($pivot_reports_query);

$entity_info = db_find('app_entities', $pivot_reports['entities_id']);

$breadcrumb = [];

$breadcrumb[] = '<li>' . link_to(
        TEXT_EXT_ITEM_PIVOT_TABLES,
        url_for('ext/item_pivot_tables/reports')
    ) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $pivot_reports['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $entity_info['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . TEXT_QUICK_FILTERS_PANELS . '</li>';

?>

<ul class="page-breadcrumb breadcrumb">
    <?php
    echo implode('', $breadcrumb) ?>
</ul>