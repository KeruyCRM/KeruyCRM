<?php

$report_info = db_find('app_ext_map_reports', str_replace('public_map', '', $app_redirect_to));

$breadcrumb = [];

$breadcrumb[] = '<li>' . link_to(
        TEXT_EXT_PIVOT_TABLES,
        url_for('ext/map_reports/reports')
    ) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $report_info['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $entity_info['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . TEXT_FILTERS . '</li>';