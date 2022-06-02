<?php

$report_info_query = db_query(
    "select * from app_ext_report_page where id='" . str_replace('report_page', '', $app_redirect_to) . "'"
);
$process_report_info = db_fetch_array($report_info_query);

$breadcrumb = [];

$breadcrumb[] = '<li>' . link_to(
        TEXT_EXT_REPORT_DESIGNER,
        url_for('ext/report_page/reports')
    ) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $process_report_info['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $entity_info['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . TEXT_FILTERS . '</li>';

