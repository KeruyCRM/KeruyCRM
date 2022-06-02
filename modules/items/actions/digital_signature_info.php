<?php

$field_query = db_query("select * from app_fields where id='" . _GET('fields_id') . "'");
if (!$field = db_fetch_array($field_query)) {
    redirect_to('dashboard/page_not_found');
}

$cfg = new fields_types_cfg($field['configuration']);


$module_info_query = db_query(
    "select * from app_ext_modules where id='" . $cfg->get(
        'module_id'
    ) . "' and type='digital_signature' and is_active=1"
);
if ($module_info = db_fetch_array($module_info_query)) {
    modules::include_module($module_info, 'digital_signature');

    $module = new $module_info['module'];
} else {
    redirect_to('dashboard/page_not_found');
}