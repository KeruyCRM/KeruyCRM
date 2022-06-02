<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_entities_templates', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_entities_templates');

    if ($entities_templates_filter > 0) {
        $obj['entities_id'] = $entities_templates_filter;
    }

    $obj['is_active'] = 1;
}