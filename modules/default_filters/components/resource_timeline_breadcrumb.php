<?php

$report_info = db_find('app_ext_resource_timeline', str_replace('resource_timeline', '', $app_redirect_to));

$breadcrumb = [];

$breadcrumb[] = '<li>' . link_to(
        TEXT_EXT_RESOURCE_TIMELINE,
        url_for('ext/resource_timeline/reports')
    ) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $report_info['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $entity_info['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . TEXT_FILTERS . '</li>';

