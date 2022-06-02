<?php

$template_info_query = db_query(
    "select ep.*, e.name as entities_name from app_ext_entities_templates ep, app_entities e where e.id=ep.entities_id and ep.id='" . db_input(
        $_GET['templates_id']
    ) . "' order by e.id, ep.sort_order, ep.name"
);
if (!$template_info = db_fetch_array($template_info_query)) {
    redirect_to('ext/templates/entities_templates');
}

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_entities_templates_fields', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_entities_templates_fields');
}
