<?php

$reports_query = db_query(
    "select * from app_ext_calendar where id='" . str_replace('calendar_report_', '', $app_redirect_to) . "'"
);
$reports = db_fetch_array($reports_query);

$breadcrumb = [];

$breadcrumb[] = '<li>' . link_to(
        TEXT_EXT_CALENDAR_REPORT,
        url_for('ext/item_pivot_tables/reports')
    ) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $reports['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $app_entities_cache[$reports['entities_id']]['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . TEXT_QUICK_FILTERS_PANELS . '</li>';

?>

<ul class="page-breadcrumb breadcrumb">
    <?php
    echo implode('', $breadcrumb) ?>
</ul>