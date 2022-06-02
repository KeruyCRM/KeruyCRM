<?php

$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_functions', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_functions');

    if ($functions_filter > 0) {
        $obj['entities_id'] = $functions_filter;
    }
}