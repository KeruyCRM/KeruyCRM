<?php

$block_info_query = db_query(
    "select b.*,f.type as field_type, f.name as field_name, f.entities_id as field_entity_id from app_ext_report_page_blocks b left join app_fields f on b.field_id=f.id  where b.id='" . str_replace(
        'report_page_block',
        '',
        $app_redirect_to
    ) . "'"
);
$block_info = db_fetch_array($block_info_query);

$report_info_query = db_query("select * from app_ext_report_page where id='" . $block_info['report_id'] . "'");
$process_report_info = db_fetch_array($report_info_query);

$breadcrumb = [];

$breadcrumb[] = '<li>' . link_to(
        TEXT_EXT_REPORT_DESIGNER,
        url_for('ext/report_page/reports')
    ) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . link_to(
        $process_report_info['name'],
        url_for('ext/report_page/blocks&report_id=' . $process_report_info['id'])
    ) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . link_to(
        TEXT_EXT_HTML_BLOCKS,
        url_for('ext/report_page/blocks&report_id=' . $process_report_info['id'])
    ) . '<i class="fa fa-angle-right"></i></li>';

if ($block_info['field_id'] > 0) {
    $name = $app_entities_cache[$block_info['field_entity_id']]['name'] . ': ' . fields_types::get_option(
            $block_info['field_type'],
            'name',
            $block_info['field_name']
        );
} else {
    $name = $block_info['name'];
}

$breadcrumb[] = '<li>' . $name . '<i class="fa fa-angle-right"></i></li>';


$breadcrumb[] = '<li>' . TEXT_FILTERS . '</li>';

