<?php

//check access
if ($app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}

if (isset($_GET['templates_id'])) {
    $template_info_query = db_query(
        "select ep.*, e.name as entities_name from app_ext_export_selected ep, app_entities e where e.id=ep.entities_id and ep.id='" . db_input(
            _GET('templates_id')
        ) . "'"
    );
    if (!$template_info = db_fetch_array($template_info_query)) {
        redirect_to('ext/export_selected/templates');
    }
}

if (isset($_GET['row_id'])) {
    $row_info_query = db_query("select b.* from app_ext_export_selected_blocks b where b.id=" . _GET('row_id'), false);
    if (!$row_info = db_fetch_array($row_info_query)) {
        redirect_to('ext/templates_docx/blocks', 'templates_id=' . $template_info['id']);
    }
}
