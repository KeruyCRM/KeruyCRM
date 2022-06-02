<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_xml_export_templates', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_xml_export_templates');

    if ($xml_templates_filter > 0) {
        $obj['entities_id'] = $xml_templates_filter;
    }

    $obj['is_active'] = 1;
    $obj['is_public'] = 0;
}