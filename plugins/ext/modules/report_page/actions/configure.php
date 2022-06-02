<?php

$report_info_query = db_query("select * from app_ext_report_page where id='" . db_input($_GET['id']) . "'");
if (!$report_info = db_fetch_array($report_info_query)) {
    redirect_to('ext/report_page/reports');
}

switch ($app_module_action) {
    case 'save':

        $sql_data = [
            'description' => $_POST['description'],
        ];

        db_perform('app_ext_report_page', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");

        redirect_to('ext/report_page/configure', 'id=' . $report_info['id']);
        break;
}