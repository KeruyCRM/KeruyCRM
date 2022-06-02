<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_import_templates', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_import_templates');

    if ($import_templates_filter > 0) {
        $obj['entities_id'] = $import_templates_filter;
    }

    $obj['is_active'] = 1;
}