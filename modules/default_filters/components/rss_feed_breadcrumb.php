<?php

$report_info_query = db_query(
    "select * from app_ext_rss_feeds where id='" . str_replace('rss_feed', '', $app_redirect_to) . "'"
);
$rss_report_info = db_fetch_array($report_info_query);

$breadcrumb = [];

$breadcrumb[] = '<li>' . link_to(
        TEXT_EXT_RSS_FEED,
        url_for('ext/rss_feed/feeds')
    ) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $rss_report_info['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $entity_info['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . TEXT_FILTERS . '</li>';
