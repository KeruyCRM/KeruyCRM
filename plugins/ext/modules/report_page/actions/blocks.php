<?php

$report_page_query = db_query("select * from app_ext_report_page where id='" . _GET('report_id') . "'");
if (!$report_page = db_fetch_array($report_page_query)) {
    redirect_to('ext/report_page/reports');
}


switch ($app_module_action) {
    case 'save':
        $sql_data = [
            'report_id' => $report_page['id'],
            'block_type' => $_POST['block_type'],
            'name' => $_POST['name'] ?? '',
            'parent_id' => 0,
            'field_id' => $_POST['field_id'] ?? 0,
            'settings' => (isset($_POST['settings']) ? json_encode($_POST['settings']) : ''),
            'sort_order' => $_POST['sort_order'],
        ];

        //print_rr($_POST);
        //EXIT();

        if (isset($_GET['id'])) {
            db_perform('app_ext_report_page_blocks', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_report_page_blocks', $sql_data);
        }

        redirect_to('ext/report_page/blocks', 'report_id=' . $report_page['id']);
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            report_page\blocks::delete(_GET('id'));

            redirect_to('ext/report_page/blocks', 'report_id=' . $report_page['id']);
        }
        break;
}