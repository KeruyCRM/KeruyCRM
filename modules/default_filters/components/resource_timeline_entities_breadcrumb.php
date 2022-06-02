<?php

$report_info_query = db_query(
    "select * from app_ext_resource_timeline where id=(select calendars_id from app_ext_resource_timeline_entities where id='" . str_replace(
        'resource_timeline_entities',
        '',
        $app_redirect_to
    ) . "')"
);
$resource_report_info = db_fetch_array($report_info_query);

$breadcrumb = [];

$breadcrumb[] = '<li>' . link_to(
        TEXT_EXT_RESOURCE_TIMELINE,
        url_for('ext/resource_timeline/reports')
    ) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $resource_report_info['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $entity_info['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . TEXT_FILTERS . '</li>';

